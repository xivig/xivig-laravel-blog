<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Widget;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseArchitectureTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_public_api_v1_posts_returns_json_resource(): void {
        $response = $this->getJson('/api/v1/posts');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'status',
                    'reading_time',
                    'views',
                    'likes',
                    'author',
                    'seo' => ['meta_title', 'score'],
                    'links' => ['self', 'api_self'],
                ],
            ],
        ]);
    }

    public function test_public_api_v1_categories_returns_json_resource(): void {
        $response = $this->getJson('/api/v1/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'posts_count',
                    'links' => ['self'],
                ],
            ],
        ]);
    }

    public function test_visitor_tracking_middleware_logs_visitor_and_page_view(): void {
        $initialVisitors = Visitor::count();
        $response = $this->get('/');
        $response->assertStatus(200);

        $this->assertDatabaseHas('visitors', [
            'ip_address' => '127.0.0.1',
        ]);
        $this->assertDatabaseHas('page_views', [
            'path' => '/',
        ]);
    }

    public function test_repository_pattern_resolves_and_fetches_posts(): void {
        $repo = app(PostRepositoryInterface::class);
        $this->assertInstanceOf(PostRepositoryInterface::class, $repo);

        $paginated = $repo->getPublishedPaginated(5);
        $this->assertGreaterThan(0, $paginated->total());

        $picks = $repo->getEditorPicks(3);
        $this->assertNotNull($picks);
    }

    public function test_menu_and_menu_items_relationship(): void {
        $headerMenu = Menu::where('location', 'header')->firstOrFail();
        $this->assertGreaterThan(0, $headerMenu->items()->count());
    }

    public function test_widgets_configuration_and_active_scope(): void {
        $widgets = Widget::active()->area('sidebar')->get();
        $this->assertGreaterThan(0, $widgets->count());
    }

    public function test_database_backup_service_generates_snapshot(): void {
        $backup = BackupService::createDatabaseBackup();
        $this->assertDatabaseHas('backups', [
            'id' => $backup->id,
            'filename' => $backup->filename,
        ]);

        // Clean up test artifact from disk
        $filePath = storage_path('app/backups/' . $backup->filename);
        if (\Illuminate\Support\Facades\File::exists($filePath)) {
            \Illuminate\Support\Facades\File::delete($filePath);
        }
    }

    public function test_roles_and_permissions_assignment(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasRole('admin'));

        $superRole = Role::where('name', 'super_admin')->firstOrFail();
        $this->assertTrue($admin->hasRole('super_admin'));
    }

    public function test_admin_enterprise_screens_render_successfully(): void {
        $admin = User::where('role', 'admin')->firstOrFail();

        // 1. Analytics
        $this->actingAs($admin)->get('/admin/analytics')->assertStatus(200)->assertSee('Audience Analytics');

        // 2. Menu Builder
        $this->actingAs($admin)->get('/admin/menus')->assertStatus(200)->assertSee('Menu Builder');

        // 3. Widgets Studio
        $this->actingAs($admin)->get('/admin/widgets')->assertStatus(200)->assertSee('Widgets & Modules');

        // 4. Backups Vault
        $this->actingAs($admin)->get('/admin/backups')->assertStatus(200)->assertSee('System Backups');

        // 5. Master SEO Studio
        $this->actingAs($admin)->get('/admin/seo')->assertStatus(200)->assertSee('Master SEO Studio');

        // 6. Roles Matrix
        $this->actingAs($admin)->get('/admin/roles')->assertStatus(200)->assertSee('Access Control & Roles');
    }
}
