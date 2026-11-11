<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterAndLegalPagesTest extends TestCase {
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

    public function test_footer_contains_populated_legal_links_and_editorial_sitemap() {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();
        preg_match('/<footer[\s\S]*?<\/footer>/i', $content, $matches);
        $this->assertNotEmpty($matches, 'Footer was not found in response HTML');
        $footerHtml = $matches[0];

        // Verify legal links are present and populated with correct routes
        $this->assertStringContainsString(route('page.show', 'privacy-policy'), $footerHtml);
        $this->assertStringContainsString(route('page.show', 'terms-of-dispatch'), $footerHtml);
        $this->assertStringContainsString(route('page.show', 'editorial-ethics'), $footerHtml);

        // Verify HTML sitemap link is present in footer (route blog.sitemap.html)
        $this->assertStringContainsString(route('blog.sitemap.html'), $footerHtml);
        $this->assertStringContainsString('Editorial Sitemap', $footerHtml);

        // Verify "RSS 2.0 Syndication" and "Search Engine XML Sitemap" are NOT in the footer
        $this->assertStringNotContainsString('RSS 2.0 Syndication', $footerHtml);
        $this->assertStringNotContainsString('Search Engine XML Sitemap', $footerHtml);
        $this->assertStringNotContainsString('/sitemap.xml', $footerHtml);
    }

    public function test_privacy_policy_page_renders_successfully() {
        $response = $this->get(route('page.show', 'privacy-policy'));
        $response->assertStatus(200);
        $response->assertSee('Privacy &amp; Confidentiality Codex', false);
    }

    public function test_terms_of_dispatch_page_renders_successfully() {
        $response = $this->get(route('page.show', 'terms-of-dispatch'));
        $response->assertStatus(200);
        $response->assertSee('Terms of Gazette Dispatch', false);
    }

    public function test_editorial_ethics_page_renders_successfully() {
        $response = $this->get(route('page.show', 'editorial-ethics'));
        $response->assertStatus(200);
        $response->assertSee('Editorial Ethics &amp; Maison Standards Codex', false);
        $response->assertSee('Uncompromised Autonomy', false);
    }

    public function test_canonical_redirects_for_legal_pages() {
        $this->get('/privacy')->assertRedirect('/page/privacy-policy');
        $this->get('/terms')->assertRedirect('/page/terms-of-dispatch');
        $this->get('/ethics')->assertRedirect('/page/editorial-ethics');
        $this->get('/about')->assertRedirect('/page/about-us');
    }

    public function test_html_sitemap_page_renders_successfully() {
        $response = $this->get('/sitemap');
        $response->assertStatus(200);
        $response->assertSee('Editorial Sitemap');
    }
}
