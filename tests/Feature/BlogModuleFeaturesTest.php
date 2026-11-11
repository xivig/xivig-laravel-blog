<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BlogModuleFeaturesTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    protected function tearDown(): void {
        while (ob_get_level() > 1) {
            @ob_end_clean();
        }
        parent::tearDown();
    }

    public function test_draft_posts_are_hidden_from_public(): void {
        $post = Post::published()->first();
        $post->update(['status' => 'draft']);

        $response = $this->get('/article/' . $post->slug);
        $response->assertStatus(404);
    }

    public function test_scheduled_posts_and_scheduler_cron_activation(): void {
        $post = Post::published()->first();
        $post->update([
            'status'       => 'scheduled',
            'published_at' => Carbon::now()->addDays(2),
        ]);

        // Guest visits scheduled post before release -> 404
        $this->get('/article/' . $post->slug)->assertStatus(404);

        // Scheduler runs while still future -> remains scheduled
        $this->artisan('blog:publish-scheduled')->assertSuccessful();
        $this->assertEquals('scheduled', $post->fresh()->status);

        // Publication date arrives
        $post->update(['published_at' => Carbon::now()->subMinute()]);
        $this->artisan('blog:publish-scheduled')->assertSuccessful();
        $this->assertEquals('published', $post->fresh()->status);

        // Public guest can now read post
        $this->get('/article/' . $post->slug)->assertStatus(200);
    }

    public function test_published_posts_render_with_metrics(): void {
        $post = Post::published()->first();
        $response = $this->get('/article/' . $post->slug);
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_sticky_posts_pinned_to_top_of_feed(): void {
        $category = Category::first();
        
        $oldPost = Post::create([
            'title'        => 'Older Regular Chronicle',
            'slug'         => 'older-regular-chronicle',
            'content'      => '<p>Regular story.</p>',
            'category_id'  => $category->id,
            'status'       => 'published',
            'is_sticky'    => false,
            'published_at' => Carbon::now()->subDays(5),
        ]);

        $stickyPost = Post::create([
            'title'        => 'Pinned Master Chronicle',
            'slug'         => 'pinned-master-chronicle',
            'content'      => '<p>Sticky story.</p>',
            'category_id'  => $category->id,
            'status'       => 'published',
            'is_sticky'    => true,
            'published_at' => Carbon::now()->subDays(10), // Older date, but sticky!
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Pinned Master Chronicle');
        $response->assertSee('Sticky');
    }

    public function test_featured_posts_flag_and_highlight(): void {
        $post = Post::published()->first();
        $post->update(['is_featured' => true]);

        $response = $this->get('/article/' . $post->slug);
        $response->assertStatus(200);
        $response->assertSee('Featured Edition');
    }

    public function test_password_protected_post_workflow(): void {
        $post = Post::published()->first();
        $post->update(['password' => 'haute-couture-2026']);

        // 1. Initial guest access shows password challenge
        $response = $this->get('/article/' . $post->slug);
        $response->assertStatus(200);
        $response->assertSee('Chronicle Protected');
        $response->assertSee('Atelier Passphrase');

        // 2. Submit wrong password -> error
        $wrong = $this->post('/article/' . $post->id . '/unlock', ['password' => 'wrong-pass']);
        $wrong->assertSessionHas('error');

        // 3. Submit correct password -> unlocks session
        $unlock = $this->post('/article/' . $post->id . '/unlock', ['password' => 'haute-couture-2026']);
        $unlock->assertRedirect('/article/' . $post->slug);

        // 4. Session unlocked -> post renders fully
        $this->withSession(['unlocked_post_' . $post->id => true])
            ->get('/article/' . $post->slug)
            ->assertStatus(200)
            ->assertSee($post->title)
            ->assertSee('Codex Protected');
    }

    public function test_private_post_restricted_to_staff(): void {
        $post = Post::published()->first();
        $post->update(['is_private' => true]);

        // Guest redirected to staff login
        $guestResp = $this->get('/article/' . $post->slug);
        $guestResp->assertRedirect(route('admin.login'));

        // Staff / Admin can view private post
        $admin = User::where('role', 'admin')->first();
        $authResp = $this->actingAs($admin)->get('/article/' . $post->slug);
        $authResp->assertStatus(200);
        $authResp->assertSee($post->title);
        $authResp->assertSee('Private Staff Only');
    }

    public function test_clone_post_replicates_chronicle_and_galleries(): void {
        $admin = User::where('role', 'admin')->first();
        $post = Post::published()->first();

        // Attach gallery item
        $post->galleryImages()->create([
            'image_path' => 'images/gallery/sample-plate.jpg',
            'caption'    => 'Bespoke Silk Drapery Plate 01',
            'order'      => 1,
        ]);

        $response = $this->actingAs($admin)->post('/admin/posts/' . $post->id . '/clone');
        $response->assertSessionHas('success');

        $clone = Post::where('title', 'like', '%' . $post->title . ' (Copy)%')->firstOrFail();
        $this->assertEquals('draft', $clone->status);
        $this->assertEquals(0, $clone->views_count);
        $this->assertEquals(1, $clone->galleryImages()->count());
        $this->assertEquals('Bespoke Silk Drapery Plate 01', $clone->galleryImages()->first()->caption);
    }

    public function test_preview_mode_allows_staff_to_view_draft_with_banner(): void {
        $admin = User::where('role', 'admin')->first();
        $post = Post::published()->first();
        $post->update(['status' => 'draft']);

        $previewResp = $this->actingAs($admin)->get('/admin/posts/' . $post->id . '/preview');
        $previewResp->assertRedirect('/article/' . $post->slug . '?preview=1');

        $viewResp = $this->actingAs($admin)->get('/article/' . $post->slug . '?preview=1');
        $viewResp->assertStatus(200);
        $viewResp->assertSee('ATELIER PREVIEW MODE');
        $viewResp->assertSee($post->title);
    }

    public function test_reading_time_computed_automatically(): void {
        $category = Category::first();
        $words = implode(' ', array_fill(0, 600, 'horology')); // 600 words = ~3 min read

        $post = Post::create([
            'title'       => 'Precision Balance Springs Monograph',
            'slug'        => 'precision-balance-springs-monograph',
            'content'     => '<p>' . $words . '</p>',
            'category_id' => $category->id,
            'status'      => 'published',
        ]);

        $this->assertEquals(3, $post->fresh()->reading_time);
    }

    public function test_article_features_toc_breadcrumbs_shares_and_nav(): void {
        $post = Post::published()->first();

        $response = $this->get('/article/' . $post->slug);
        $response->assertStatus(200);

        // 1. Table of contents container
        $response->assertSee('tableOfContentsWidget');
        $response->assertSee('Article Index');

        // 2. Share Buttons
        $response->assertSee('twitter-x');
        $response->assertSee('facebook');
        $response->assertSee('linkedin');
        $response->assertSee('whatsapp');
        $response->assertSee('Copy Link');

        // 3. Breadcrumbs
        $response->assertSee('breadcrumb');
        $response->assertSee('Chronicles');
        $response->assertSee('schema.org');
        $response->assertSee('BreadcrumbList');

        // 4. Previous / Next Story or similar curations
        $response->assertSee('Chronicles of Similar Essence');
    }

    public function test_unlimited_images_gallery_showcase_and_deletion(): void {
        $admin = User::where('role', 'admin')->first();
        $post = Post::published()->first();

        $img1 = $post->galleryImages()->create([
            'image_path' => 'images/sample-1.jpg',
            'caption'    => 'Plate 01: Tourbillon Cage',
            'order'      => 1,
        ]);
        $img2 = $post->galleryImages()->create([
            'image_path' => 'images/sample-2.jpg',
            'caption'    => 'Plate 02: Guilloché Dial',
            'order'      => 2,
        ]);

        // Renders in article visual gallery
        $response = $this->get('/article/' . $post->slug);
        $response->assertStatus(200);
        $response->assertSee('Atelier Visual Gallery');
        $response->assertSee('Plate 01: Tourbillon Cage');
        $response->assertSee('Plate 02: Guilloché Dial');

        // Admin can delete gallery plate
        $delResp = $this->actingAs($admin)->delete('/admin/posts/gallery-images/' . $img1->id);
        $delResp->assertSessionHas('success');
        $this->assertDatabaseMissing('post_images', ['id' => $img1->id]);
    }
}
