<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\NotFoundLog;
use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogAdminTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_unauthenticated_user_cannot_access_admin(): void {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');

        $dashResponse = $this->get('/admin/dashboard');
        $dashResponse->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_and_access_dashboard(): void {
        $response = $this->post('/admin/login', [
            'email'    => 'admin@xivig.com',
            'password' => 'admin123456',
        ]);
        $response->assertRedirect('/admin');

        $this->assertAuthenticated();

        $dashResponse = $this->get('/admin');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Editorial Overview');
        $dashResponse->assertSee('Published Chronicles');
    }

    public function test_admin_can_access_dashboard_via_explicit_dashboard_url(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('MAISON ADMINISTRATIVE OS 2.0');
        $response->assertSee('Published Chronicles');
        $response->assertSee('Recent Editorial Stories');
    }

    public function test_admin_search_returns_results(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        $response = $this->actingAs($admin)->get('/admin/search?query=couture');
        $response->assertStatus(200);
        $response->assertSee('Search Results for:');
    }

    public function test_admin_can_create_edit_and_delete_posts(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        $category = Category::firstOrFail();

        // 1. Create Post
        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title'       => 'The Alchemy of Silk Weaving',
            'category_id' => $category->id,
            'excerpt'     => 'An exploration into Italian looms.',
            'content'     => '<p>Silk weaving requires patience.</p>',
            'status'      => 'published',
        ]);
        $response->assertRedirect('/admin/posts');
        $this->assertDatabaseHas('posts', ['title' => 'The Alchemy of Silk Weaving']);

        $post = Post::where('title', 'The Alchemy of Silk Weaving')->firstOrFail();

        // 2. Edit Post
        $updateResp = $this->actingAs($admin)->put('/admin/posts/' . $post->id, [
            'title'       => 'The Alchemy of Silk Weaving in Lake Como',
            'category_id' => $category->id,
            'content'     => '<p>Updated content with rich narrative.</p>',
            'status'      => 'published',
        ]);
        $updateResp->assertRedirect('/admin/posts');
        $this->assertDatabaseHas('posts', ['title' => 'The Alchemy of Silk Weaving in Lake Como']);

        // 3. Delete Post
        $delResp = $this->actingAs($admin)->delete('/admin/posts/' . $post->id);
        $delResp->assertRedirect('/admin/posts');
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_admin_can_moderate_comments(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        $comment = Comment::firstOrFail();

        // 1. Approve comment
        $response = $this->actingAs($admin)->post('/admin/comments/' . $comment->id . '/approve');
        $response->assertStatus(302);
        $this->assertEquals('approved', $comment->fresh()->status);

        // 2. Mark spam
        $spamResp = $this->actingAs($admin)->post('/admin/comments/' . $comment->id . '/spam');
        $spamResp->assertStatus(302);
        $this->assertEquals('spam', $comment->fresh()->status);
    }

    public function test_admin_can_clone_post(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        $post = Post::published()->firstOrFail();

        $response = $this->actingAs($admin)->post('/admin/posts/' . $post->id . '/clone');
        $cloned = Post::where('title', $post->title . ' (Copy)')->firstOrFail();
        $response->assertRedirect(route('admin.posts.edit', $cloned->id));

        $this->assertEquals('draft', $cloned->status);
        $this->assertEquals($post->title . ' (Copy)', $cloned->title);
    }

    public function test_admin_can_manage_pages(): void {
        $admin = User::where('role', 'admin')->firstOrFail();

        $indexResp = $this->actingAs($admin)->get('/admin/pages');
        $indexResp->assertStatus(200);
        $indexResp->assertSee('Static CMS Pages');

        $createResp = $this->actingAs($admin)->post('/admin/pages', [
            'title'            => 'Bespoke Atelier Standards',
            'content'          => '<p>Strict principles of artisanal precision.</p>',
            'meta_title'       => 'Artisanal Standards | XIVIG',
            'meta_description' => 'Precision and criteria for Maison curation.',
            'is_published'     => 1,
        ]);
        $createResp->assertRedirect('/admin/pages');
        $this->assertDatabaseHas('pages', ['title' => 'Bespoke Atelier Standards']);
    }

    public function test_admin_can_manage_redirects(): void {
        $admin = User::where('role', 'admin')->firstOrFail();

        $indexResp = $this->actingAs($admin)->get('/admin/redirects');
        $indexResp->assertStatus(200);
        $indexResp->assertSee('Redirect Manager');

        $storeResp = $this->actingAs($admin)->post('/admin/redirects', [
            'source_url'  => '/old-vintage-link',
            'target_url'  => '/new-vintage-link',
            'status_code' => 301,
            'is_active'   => 1,
        ]);
        $storeResp->assertRedirect('/admin/redirects');
        $this->assertDatabaseHas('redirects', [
            'source_url' => '/old-vintage-link',
            'target_url' => '/new-vintage-link',
        ]);
    }

    public function test_admin_can_view_and_clear_not_found_logs(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        NotFoundLog::logUrl('/test-broken-link-admin', 'https://google.com', 'TestBot/1.0', '127.0.0.1');

        $indexResp = $this->actingAs($admin)->get('/admin/not-found-logs');
        $indexResp->assertStatus(200);
        $indexResp->assertSee('/test-broken-link-admin');

        $clearResp = $this->actingAs($admin)->post('/admin/not-found-logs/clear');
        $clearResp->assertRedirect('/admin/not-found-logs');
        $this->assertDatabaseCount('not_found_logs', 0);
    }
}
