<?php

namespace Tests\Feature;

use App\Mail\TwoFactorOtpMail;
use App\Models\Comment;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Services\HtmlSanitizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    /**
     * 1. CRITICAL: Public registration is completely disabled.
     */
    public function test_public_registration_routes_are_disabled_and_redirect_to_login(): void {
        $getRoutes = ['/register', '/admin/register'];
        foreach ($getRoutes as $url) {
            $response = $this->get($url);
            $response->assertRedirect(route('admin.login'));
            $response->assertSessionHas('error', 'Public staff registration is disabled. Staff accounts must be provisioned by a System Administrator.');
        }

        $initialUserCount = User::count();

        $postRoutes = ['/register', '/admin/register'];
        foreach ($postRoutes as $url) {
            $response = $this->post($url, [
                'name'                  => 'Malicious Actor',
                'email'                 => 'attacker@example.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]);
            $response->assertRedirect(route('admin.login'));
            $response->assertSessionHas('error', 'Public staff registration is disabled. Staff accounts must be provisioned by a System Administrator.');
        }

        // Verify zero new users were created
        $this->assertEquals($initialUserCount, User::count());
        $this->assertDatabaseMissing('users', ['email' => 'attacker@example.com']);
    }

    /**
     * 2. CRITICAL: 2FA OTP is cryptographically secure, hashed in DB, mailed, and not leaked.
     */
    public function test_two_factor_otp_security_and_hashed_storage(): void {
        Mail::fake();

        $this->admin->update([
            'two_factor_enabled' => true,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $this->admin->email,
            'password' => 'admin123456',
        ]);

        $response->assertRedirect(route('admin.two-factor'));
        $this->admin->refresh();

        // 1. Assert DB stores a secure bcrypt hash, NEVER plain text
        $this->assertNotNull($this->admin->two_factor_code);
        $this->assertStringStartsWith('$2y$', $this->admin->two_factor_code);
        $this->assertTrue($this->admin->two_factor_expires_at->isFuture());

        // 2. Assert Mailable was dispatched to user
        Mail::assertSent(TwoFactorOtpMail::class, function ($mail) {
            return $mail->hasTo($this->admin->email) && strlen($mail->otp) === 6 && ctype_digit($mail->otp);
        });

        // 3. Assert OTP is NOT leaked in session flash messages or UI
        $response->assertSessionMissing('dev_code');
        $flashStatus = session('status') ?? '';
        $this->assertStringNotContainsString('Dev Code', $flashStatus);

        // 4. Assert 2FA page does not expose plaintext OTP
        $pageResponse = $this->withSession(['auth.2fa.id' => $this->admin->id])->get(route('admin.two-factor'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertDontSee('dev-code-container');
        $pageResponse->assertDontSee('Dev Bypass Code');
    }

    /**
     * 2b. CRITICAL: 2FA verification enforces maximum 5 attempts and triggers lockout.
     */
    public function test_two_factor_lockout_after_max_failed_attempts(): void {
        Mail::fake();

        $this->admin->update([
            'two_factor_enabled' => true,
            'two_factor_code' => Hash::make('654321'),
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        $session = [
            'auth.2fa.id' => $this->admin->id,
            'auth.2fa.attempts' => 0,
        ];

        // Fail attempts 1 to 4: validation error on code
        for ($i = 1; $i <= 4; $i++) {
            $resp = $this->withSession($session)->post(route('admin.two-factor.verify'), [
                'code' => '000000',
            ]);
            $session['auth.2fa.attempts'] = $i;
            $resp->assertSessionHasErrors('code');
        }

        // 5th failed attempt triggers immediate lockout and code invalidation
        $lockoutResp = $this->withSession($session)->post(route('admin.two-factor.verify'), [
            'code' => '000000',
        ]);

        $lockoutResp->assertRedirect(route('admin.login'));
        $lockoutResp->assertSessionHas('error');

        $this->admin->refresh();
        $this->assertNull($this->admin->two_factor_code);
        $this->assertNull($this->admin->two_factor_expires_at);
    }

    /**
     * 3. HIGH: AST-based HTML sanitization for articles, pages, and comments.
     */
    public function test_html_sanitizer_removes_malicious_vectors_while_preserving_safe_markup(): void {
        $maliciousPayload = '
            <h1>Fine Horology</h1>
            <p>An exquisite tourbillon mechanism.<script>alert("XSS")</script></p>
            <a href="javascript:alert(1)">Click here</a>
            <a href="https://example.com/luxury" target="_blank">Valid Link</a>
            <img src="https://example.com/watch.jpg" onerror="alert(2)" alt="Watch">
            <iframe src="https://evil.com" onload="alert(3)"></iframe>
            <div style="background-image: url(javascript:alert(4)); color: red;">Styled Div</div>
        ';

        // Article Sanitization
        $cleanArticle = HtmlSanitizerService::cleanArticle($maliciousPayload);
        $this->assertStringNotContainsString('<script', $cleanArticle);
        $this->assertStringNotContainsString('javascript:', $cleanArticle);
        $this->assertStringNotContainsString('onerror', $cleanArticle);
        $this->assertStringNotContainsString('<iframe', $cleanArticle);
        $this->assertStringNotContainsString('alert(', $cleanArticle);
        $this->assertStringContainsString('<h1>Fine Horology</h1>', $cleanArticle);
        $this->assertStringContainsString('https://example.com/luxury', $cleanArticle);
        $this->assertStringContainsString('rel="noopener noreferrer"', $cleanArticle);

        // Page Sanitization
        $cleanPage = HtmlSanitizerService::cleanPage($maliciousPayload);
        $this->assertStringNotContainsString('<script', $cleanPage);
        $this->assertStringNotContainsString('javascript:', $cleanPage);
        $this->assertStringNotContainsString('onerror', $cleanPage);
        $this->assertStringNotContainsString('<iframe', $cleanPage);

        // Comment Sanitization
        $cleanComment = HtmlSanitizerService::cleanComment($maliciousPayload);
        $this->assertStringNotContainsString('<script', $cleanComment);
        $this->assertStringNotContainsString('javascript:', $cleanComment);
        $this->assertStringNotContainsString('<h1', $cleanComment); // h1 not in comment allowed tags
    }

    /**
     * 3b. HIGH: Model Observers automatically sanitize Post and Page content on save.
     */
    public function test_model_observers_automatically_sanitize_post_and_page_content(): void {
        $page = Page::create([
            'title' => 'Security Audit Policy',
            'slug' => 'security-audit-policy',
            'content' => '<p>Institutional guidelines.</p><script>alert("pwned")</script>',
            'is_published' => true,
        ]);

        $this->assertStringNotContainsString('<script>', $page->fresh()->content);
        $this->assertStringContainsString('Institutional guidelines.', $page->fresh()->content);

        $post = Post::create([
            'title' => 'New Chronicle',
            'slug' => 'new-chronicle-security',
            'content' => '<p>Editorial note.</p><script>alert("hacked")</script>',
            'status' => 'published',
        ]);

        $this->assertStringNotContainsString('<script>', $post->fresh()->content);
        $this->assertStringContainsString('Editorial note.', $post->fresh()->content);
    }

    /**
     * 3c. HIGH: JSON-LD blocks escape script tags and special chars.
     */
    public function test_json_ld_blocks_prevent_script_injection_breakout(): void {
        $obLevel = ob_get_level();

        $post = Post::create([
            'title' => 'Breaker </script><script>alert("XSS")</script>',
            'slug' => 'breaker-post-test',
            'content' => '<p>Safe test post</p>',
            'status' => 'published',
        ]);

        $response = $this->get(route('blog.show', $post->slug));
        $response->assertStatus(200);

        // The HTML response must NOT contain an unescaped literal </script><script>alert("XSS")</script>
        $content = $response->getContent();
        $this->assertStringNotContainsString('</script><script>alert("XSS")</script>', $content);
        // It must be safely unicode escaped
        $this->assertStringContainsString('\u003C', $content);

        while (ob_get_level() > $obLevel) {
            @ob_end_clean();
        }
    }

    /**
     * 4. HIGH: Admin login rate limiter is registered and blocks excessive attempts.
     */
    public function test_login_rate_limiter_throttles_excessive_failed_attempts(): void {
        RateLimiter::clear(RateLimiter::availableIn('admin-login'));

        $url = route('admin.login.submit');

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post($url, [
                'email' => 'brute@example.com',
                'password' => 'wrongpass',
            ]);
            $response->assertStatus(302);
        }

        // 6th attempt should be blocked by rate limiting (either HTTP 429 or lockout redirect with error)
        $blockedResponse = $this->post($url, [
            'email' => 'brute@example.com',
            'password' => 'wrongpass',
        ]);

        $this->assertTrue(
            $blockedResponse->status() === 429 || $blockedResponse->isRedirect()
        );

        if ($blockedResponse->isRedirect()) {
            $blockedResponse->assertSessionHasErrors('email');
            $errors = session('errors')->get('email');
            $this->assertTrue(
                collect($errors)->contains(fn($msg) => str_contains($msg, 'Too many') || str_contains($msg, 'locked'))
            );
        }
    }
}
