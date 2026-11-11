<?php

namespace Tests\Feature;

use App\Services\HtmlSanitizerService;
use App\Services\SecureUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BlogSecurityTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_security_headers_are_present_on_all_responses(): void {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_html_sanitizer_neutralizes_xss_scripts_and_event_handlers(): void {
        $maliciousPayload = '<p>Normal text <script>alert("XSS Attack!");</script> <img src="x" onerror="stealCookies()"> <a href="javascript:alert(1)">Click me</a></p>';
        
        $clean = HtmlSanitizerService::cleanArticle($maliciousPayload);

        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('Normal text', $clean);
    }

    public function test_secure_upload_rejects_non_whitelisted_mimes(): void {
        $this->expectException(\InvalidArgumentException::class);

        // Create dummy fake executable/php file disguised as an image
        $fakeFile = UploadedFile::fake()->create('malicious.php', 10, 'application/x-php');
        SecureUploadService::upload($fakeFile);
    }
}
