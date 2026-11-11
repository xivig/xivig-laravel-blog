<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapAndFeedVerificationTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_xml_sitemap_returns_valid_xml_with_xsl_stylesheet(): void {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        
        $content = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('xml-stylesheet type="text/xsl"', $content);
        $this->assertStringContainsString('sitemap.xsl', $content);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', $content);
        $this->assertStringContainsString('<priority>1.0</priority>', $content);
    }

    public function test_sitemap_xsl_is_accessible_with_correct_mime_type(): void {
        $response = $this->get('/sitemap.xsl');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/xsl', $response->headers->get('Content-Type'));
        
        $content = $response->getContent();
        $this->assertStringContainsString('Master XML Sitemap Codex', $content);
        $this->assertStringContainsString('xsl:stylesheet', $content);
    }

    public function test_rss_feed_returns_valid_rss_with_media_and_xsl_stylesheet(): void {
        $response = $this->get('/feed.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');

        $content = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('xml-stylesheet type="text/xsl"', $content);
        $this->assertStringContainsString('rss.xsl', $content);
        $this->assertStringContainsString('<rss version="2.0"', $content);
        $this->assertStringContainsString('xmlns:atom="http://www.w3.org/2005/Atom"', $content);
        $this->assertStringContainsString('xmlns:media="http://search.yahoo.com/mrss/"', $content);
        $this->assertStringContainsString('<channel>', $content);
    }

    public function test_rss_feed_aliases_work_seamlessly(): void {
        $this->get('/rss')->assertStatus(200)->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $this->get('/rss.xml')->assertStatus(200)->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        
        // /feed redirects to /feed.xml or resolves cleanly
        $res = $this->get('/feed');
        if ($res->status() === 301) {
            $res->assertRedirect('/feed.xml');
            $this->get('/feed.xml')->assertStatus(200);
        } else {
            $res->assertStatus(200);
        }
    }

    public function test_rss_xsl_is_accessible_with_correct_mime_type(): void {
        $response = $this->get('/rss.xsl');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/xsl', $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $this->assertStringContainsString('RSS 2.0', $content);
        $this->assertStringContainsString('xsl:stylesheet', $content);
    }

    public function test_public_html_sitemap_directory_renders_successfully(): void {
        $response = $this->get('/sitemap');

        $response->assertStatus(200);
        $response->assertSee('The Master Editorial Sitemap');
        $response->assertSee('Editorial Channels');
        $response->assertSee('Institutional Codex');
        $response->assertSee('Atelier Services');
    }
}
