<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    public function test_all_error_blade_views_exist_in_both_directories(): void
    {
        $views = ['400', '403', '404', '500', '503', 'maintenance'];

        foreach ($views as $view) {
            $this->assertTrue(view()->exists("errors.{$view}"), "View errors.{$view} must exist");
            $this->assertTrue(view()->exists("pages.error.{$view}"), "View pages.error.{$view} must exist");
        }
    }

    public function test_unregistered_route_renders_elite_404_page(): void
    {
        $response = $this->get('/unregistered-slug-xyz-random-404');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Page Not Found');
        $response->assertSee('Maison Gazette');
        $response->assertSee('Back to Gazette');
        $response->assertSee('themeToggleBtn');
        $response->assertSee('data-bs-theme');
    }

    public function test_login_and_register_pages_contain_theme_switcher(): void
    {
        $loginRes = $this->get(route('admin.login'));
        $loginRes->assertOk();
        $loginRes->assertSee('themeToggleBtn');
        $loginRes->assertSee('data-bs-theme');

        // Registration route safely redirects to login with security notice
        $regRes = $this->get(route('admin.register'));
        $regRes->assertRedirect(route('admin.login'));
        $regRes->assertSessionHas('error');
    }

    public function test_admin_error_showcase_index_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.errors.index'));

        $response->assertOk();
        $response->assertSee('Error Views &amp; Status Templates', false);
        $response->assertSee('400');
        $response->assertSee('403');
        $response->assertSee('404');
        $response->assertSee('500');
        $response->assertSee('503');
        $response->assertSee('Refining Excellence');
    }

    public function test_admin_error_previews_render_expected_status_and_content(): void
    {
        $previewRoutes = [
            'admin.errors.400' => [400, 'Bad Request'],
            'admin.errors.403' => [403, 'Clearance Required'],
            'admin.errors.404' => [404, 'Page Not Found'],
            'admin.errors.500' => [500, 'Server Exception'],
            'admin.errors.503' => [503, 'Fine-Tuning Architecture'],
            'admin.errors.maintenance' => [503, 'Refining Excellence'],
        ];

        foreach ($previewRoutes as $routeName => [$statusCode, $expectedContent]) {
            $response = $this->actingAs($this->admin)->get(route($routeName));

            $response->assertStatus($statusCode);
            $response->assertSee($expectedContent);
            $response->assertSee('Maison Gazette');
        }
    }

    public function test_guest_is_redirected_from_admin_error_showcase(): void
    {
        $response = $this->get(route('admin.errors.index'));

        $response->assertRedirect(route('admin.login'));
    }
}
