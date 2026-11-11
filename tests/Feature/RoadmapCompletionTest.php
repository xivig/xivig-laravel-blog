<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\Category;
use App\Models\Post;
use App\Models\Revision;
use App\Models\User;
use App\Services\SeoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RoadmapCompletionTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_password_reset_does_not_leak_link_and_prevents_user_enumeration(): void {
        Mail::fake();

        $admin = User::create([
            'name'     => 'Security Officer',
            'email'    => 'security.officer@xivig.test',
            'role'     => 'admin',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $response = $this->post(route('admin.password.email'), [
            'email' => 'security.officer@xivig.test',
        ]);

        $response->assertSessionHas('status');
        $statusMsg = session('status');

        // Status message MUST NOT contain raw URL or token HTML
        $this->assertStringNotContainsString('route(', $statusMsg);
        $this->assertStringNotContainsString('admin/password/reset', $statusMsg);
        $this->assertStringContainsString('If an active administrative account is associated', $statusMsg);

        // Verify email dispatched
        Mail::assertSent(PasswordResetMail::class, function ($mail) use ($admin) {
            return $mail->user->id === $admin->id && !empty($mail->resetUrl);
        });

        // Non-existent user gets identical message (prevents user enumeration)
        $anonResponse = $this->post(route('admin.password.email'), [
            'email' => 'unknown@nowhere.test',
        ]);
        $anonResponse->assertSessionHas('status');
        $this->assertEquals($statusMsg, session('status'));
    }

    public function test_password_reset_enforces_expiration(): void {
        $editor = User::create([
            'name'     => 'Editor Test',
            'email'    => 'editor.test@xivig.test',
            'role'     => 'editor',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $token = 'expired_sample_token_1234567890';
        DB::table('password_reset_tokens')->insert([
            'email'      => 'editor.test@xivig.test',
            'token'      => Hash::make($token),
            'created_at' => Carbon::now()->subMinutes(90), // Expired (> 60 mins)
        ]);

        $response = $this->post(route('admin.password.update'), [
            'token'                 => $token,
            'email'                 => 'editor.test@xivig.test',
            'password'              => 'NewSecurePass999!',
            'password_confirmation' => 'NewSecurePass999!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertTrue(Hash::check('OldPassword123!', $editor->fresh()->password));
    }

    public function test_post_revision_history_and_rollback_lifecycle(): void {
        $admin = User::where('role', 'admin')->firstOrFail();
        $post = Post::create([
            'title'   => 'Original Historic Title',
            'slug'    => 'original-historic-title',
            'content' => '<p>Original historic chronicle content paragraph.</p>',
            'status'  => 'published',
        ]);

        // Create an archival revision snapshot
        $revision = Revision::create([
            'user_id'           => $admin->id,
            'revisionable_type' => Post::class,
            'revisionable_id'   => $post->id,
            'title'             => 'Original Historic Title',
            'content'           => '<p>Original historic chronicle content paragraph.</p>',
            'summary'           => 'Initial baseline snapshot',
        ]);

        // Modify post
        $post->update([
            'title'   => 'Heavily Altered Title',
            'content' => '<p>Completely different altered content.</p>',
        ]);

        // Browse revisions view as admin
        $viewResponse = $this->actingAs($admin)->get(route('admin.posts.revisions', $post->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Snapshot #' . $revision->id);
        $viewResponse->assertSee('Original Historic Title');

        // Execute rollback to historical revision
        $restoreResponse = $this->actingAs($admin)->post(route('admin.posts.revisions.restore', [
            'post'     => $post->id,
            'revision' => $revision->id,
        ]));

        $restoreResponse->assertRedirect(route('admin.posts.edit', $post->id));
        $restoreResponse->assertSessionHas('success');

        // Verify post restored
        $freshPost = $post->fresh();
        $this->assertEquals('Original Historic Title', $freshPost->title);
        $this->assertStringContainsString('Original historic chronicle content', $freshPost->content);
    }

    public function test_seo_service_suggest_internal_links(): void {
        $cat = Category::create([
            'name' => 'High Horology Test',
            'slug' => 'high-horology-test',
        ]);

        $postA = Post::create([
            'title'        => 'Patek Philippe Celestial Complication Unique',
            'slug'         => 'patek-philippe-celestial-unique',
            'content'      => '<p>Detailed article discussing Patek complications.</p>',
            'category_id'  => $cat->id,
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $postB = Post::create([
            'title'        => 'Vacheron Constantin Tourbillon Craft Unique',
            'slug'         => 'vacheron-constantin-tourbillon-unique',
            'content'      => '<p>Detailed article discussing Vacheron complications.</p>',
            'category_id'  => $cat->id,
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $suggestions = SeoService::suggestInternalLinks($postA);
        $this->assertNotEmpty($suggestions);
        $this->assertEquals($postB->id, $suggestions[0]['id']);
        $this->assertStringContainsString('Shares category', $suggestions[0]['reason']);
        $this->assertStringContainsString(route('blog.show', $postB->slug), $suggestions[0]['anchor_html']);
    }
}
