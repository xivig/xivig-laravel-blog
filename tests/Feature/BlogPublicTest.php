<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPublicTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_renders_successfully_with_hero_and_picks(): void {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('XIVIG');
        $response->assertSee('MAISON GAZETTE');
        $response->assertSee('Feature of the Maison');
    }

    public function test_article_page_renders_and_increments_views(): void {
        $obLevel = ob_get_level();
        $post = Post::published()->firstOrFail();
        $initialViews = $post->views_count;

        $response = $this->get('/article/' . $post->slug);
        $response->assertStatus(200);
        $response->assertSee($post->title);

        $this->assertEquals($initialViews + 1, $post->fresh()->views_count);

        while (ob_get_level() > $obLevel) {
            @ob_end_clean();
        }
    }

    public function test_category_page_filters_posts(): void {
        $category = Category::where('slug', 'haute-couture')->firstOrFail();
        $response = $this->get('/category/' . $category->slug);
        $response->assertStatus(200);
        $response->assertSee($category->name);
    }

    public function test_tag_page_filters_posts(): void {
        $tag = Tag::firstOrFail();
        $response = $this->get('/tag/' . $tag->slug);
        $response->assertStatus(200);
        $response->assertSee('#' . $tag->name);
    }

    public function test_search_page_finds_articles(): void {
        $post = Post::published()->firstOrFail();
        $firstWord = explode(' ', $post->title)[0];

        $response = $this->get('/search?q=' . urlencode($firstWord));
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_live_search_api_returns_json(): void {
        $post = Post::published()->firstOrFail();
        $firstWord = explode(' ', $post->title)[0];

        $response = $this->getJson('/api/search?q=' . urlencode($firstWord));
        $response->assertStatus(200);
        $response->assertJsonStructure(['results' => []]);
    }

    public function test_like_article_api_increments_count(): void {
        $post = Post::published()->firstOrFail();
        $initialLikes = $post->likes_count;

        $response = $this->postJson('/article/' . $post->id . '/like');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals($initialLikes + 1, $post->fresh()->likes_count);
    }

    public function test_submit_comment_creates_record_and_honeypot_discards_bot(): void {
        $post = Post::published()->firstOrFail();

        // 1. Legitimate user comment
        $response = $this->post('/article/' . $post->id . '/comments', [
            'author_name'  => 'Camille Saint',
            'author_email' => 'camille@atelier.com',
            'content'      => 'A delightful critique on modern textile geometry.',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('comments', [
            'author_name' => 'Camille Saint',
            'post_id'     => $post->id,
        ]);

        // 2. Bot submission with filled honeypot (should be dropped silently)
        $responseBot = $this->post('/article/' . $post->id . '/comments', [
            'author_name'        => 'SpamBot',
            'author_email'       => 'bot@spam.com',
            'content'            => 'Buy cheap watches now!',
            'website_url_check'  => 'http://spamsite.xyz',
        ]);
        $responseBot->assertStatus(302);
        $this->assertDatabaseMissing('comments', [
            'author_name' => 'SpamBot',
        ]);
    }

    public function test_newsletter_subscription_creates_subscriber(): void {
        $response = $this->postJson('/newsletter/subscribe', [
            'email' => 'patron@luxurygazette.com',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'patron@luxurygazette.com',
        ]);
    }

    public function test_rss_feed_is_valid_xml(): void {
        $response = $this->get('/feed.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $response->assertSee('<rss version="2.0"', false);
    }

    public function test_sitemap_is_valid_xml(): void {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<urlset', false);
    }

    public function test_robots_txt_renders_successfully(): void {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /admin');
        $response->assertSee('Sitemap:');
    }

    public function test_contact_page_renders_and_submits_message(): void {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Editorial Correspondence');

        $postResponse = $this->post('/contact', [
            'name'    => 'Lord Archibald',
            'email'   => 'archibald@mayfair.co.uk',
            'subject' => 'Atelier Private Viewing',
            'message' => 'Kindly reserve a private viewing salon for our private collectors.',
        ]);
        $postResponse->assertSessionHas('contact_success');
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'archibald@mayfair.co.uk',
            'subject' => 'Atelier Private Viewing',
        ]);
    }

    public function test_contact_honeypot_discards_spam(): void {
        $botResponse = $this->post('/contact', [
            'name'             => 'Spambot',
            'email'            => 'bot@spam.com',
            'subject'          => 'Spam Links',
            'message'          => 'Buy cheap backlinks here for your site',
            'contact_honeypot' => 'http://spam-link-bot.com', // Honeypot field filled
        ]);
        $botResponse->assertStatus(302);
        $this->assertDatabaseMissing('contact_messages', [
            'email' => 'bot@spam.com',
        ]);
    }

    public function test_static_cms_page_renders_published_page(): void {
        $response = $this->get('/page/about-us');
        $response->assertStatus(200);
        $response->assertSee('About XIVIG Maison Gazette');
    }

    public function test_redirect_middleware_redirects_configured_urls(): void {
        $response = $this->get('/legacy-editorial/couture-origins');
        $response->assertStatus(301);
        $response->assertRedirect('/chronicles');
    }

    public function test_404_logs_incident_to_database(): void {
        $response = $this->get('/unknown-missing-page-slug-xyz');
        $response->assertStatus(404);
        $this->assertDatabaseHas('not_found_logs', [
            'url' => '/unknown-missing-page-slug-xyz',
        ]);
    }
}
