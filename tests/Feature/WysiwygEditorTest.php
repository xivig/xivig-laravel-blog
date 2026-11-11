<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WysiwygEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > 1) {
            @ob_end_clean();
        }
        parent::tearDown();
    }

    public function test_admin_post_create_view_contains_wysiwyg_editor(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->get(route('admin.posts.create'));

        $response->assertStatus(200);
        $response->assertSee('wysiwyg-editor');
        $response->assertSee('wysiwyg.js');
        $response->assertSee('summernote-bs5.min.css');
        $response->assertSee('tinymce');
    }

    public function test_admin_post_edit_view_contains_wysiwyg_editor(): void
    {
        $admin = User::first();
        $post = Post::first();

        $response = $this->actingAs($admin)->get(route('admin.posts.edit', $post->id));

        $response->assertStatus(200);
        $response->assertSee('wysiwyg-editor');
        $response->assertSee('wysiwyg.js');
    }

    public function test_admin_page_create_view_contains_wysiwyg_editor(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->get(route('admin.pages.create'));

        $response->assertStatus(200);
        $response->assertSee('wysiwyg-editor');
        $response->assertSee('wysiwyg.js');
    }

    public function test_admin_page_edit_view_contains_wysiwyg_editor(): void
    {
        $admin = User::first();
        $page = Page::first();

        $response = $this->actingAs($admin)->get(route('admin.pages.edit', $page->id));

        $response->assertStatus(200);
        $response->assertSee('wysiwyg-editor');
        $response->assertSee('wysiwyg.js');
    }

    public function test_media_upload_endpoint_returns_wysiwyg_compatible_json(): void
    {
        Storage::fake('public');

        $admin = User::first();
        $fakeImage = UploadedFile::fake()->image('editorial-plate.jpg', 800, 600);

        $response = $this->actingAs($admin)->postJson(route('admin.media.upload'), [
            'file' => $fakeImage,
            'alt_text' => 'Bespoke Atelier Craftsmanship',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'location',
            'url',
            'id',
        ]);
        $response->assertJson([
            'success' => true,
        ]);
    }
}
