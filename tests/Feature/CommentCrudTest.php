<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentCrudTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;
    protected Post $post;
    protected Comment $comment;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();

        $this->post = Post::firstOrFail();

        $this->comment = Comment::create([
            'post_id'      => $this->post->id,
            'user_id'      => $this->admin->id,
            'author_name'  => 'Camille Desmoulins',
            'author_email' => 'camille@xivig.com',
            'content'      => 'A splendid perspective on contemporary Parisian fashion dynamics.',
            'status'       => 'approved',
        ]);
    }

    public function test_comment_index_renders_with_metrics_and_activity(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.comments.index'));
        $response->assertStatus(200);
        $response->assertSee('Discourse & Comments Moderation');
        $response->assertSee('Camille Desmoulins');
        $response->assertSee('A splendid perspective');
        $response->assertSee('Active Discourse');
        $response->assertSee('Trash Vault');
    }

    public function test_comment_create_screen_renders(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.comments.create'));
        $response->assertStatus(200);
        $response->assertSee('New Discourse Commentary');
        $response->assertSee('Target Chronicle / Article');
        $response->assertSee('Author Identity');
    }

    public function test_comment_store_creates_comment(): void {
        $response = $this->actingAs($this->admin)->post(route('admin.comments.store'), [
            'post_id' => $this->post->id,
            'user_id' => $this->admin->id,
            'comment' => 'Extraordinary editorial curation and visual tapestry.',
        ]);

        $response->assertRedirect(route('admin.comments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'post_id'     => $this->post->id,
            'user_id'     => $this->admin->id,
            'author_name' => $this->admin->name,
            'content'     => 'Extraordinary editorial curation and visual tapestry.',
            'status'      => 'approved',
        ]);
    }

    public function test_comment_show_details_screen_renders(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.comments.show', $this->comment->id));
        $response->assertStatus(200);
        $response->assertSee('Discourse Thread Details');
        $response->assertSee('Camille Desmoulins');
        $response->assertSee($this->comment->content);
        $response->assertSee($this->post->title);
    }

    public function test_comment_edit_screen_renders(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.comments.edit', $this->comment->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Discourse Remark');
        $response->assertSee($this->comment->content);
    }

    public function test_comment_update_modifies_content_and_status(): void {
        $updatedText = 'Revised analytical remark regarding silhouette lines.';
        $response = $this->actingAs($this->admin)->put(route('admin.comments.update', $this->comment->id), [
            'post_id' => $this->post->id,
            'comment' => $updatedText,
            'status'  => 'approved',
        ]);

        $response->assertRedirect(route('admin.comments.index'));
        $response->assertSessionHas('success');

        $this->comment->refresh();
        $this->assertEquals($updatedText, $this->comment->content);
    }

    public function test_comment_approve_and_spam_toggles(): void {
        $this->comment->update(['status' => 'pending']);

        // Approve
        $approveResponse = $this->actingAs($this->admin)->post(route('admin.comments.approve', $this->comment->id));
        $approveResponse->assertRedirect();
        $approveResponse->assertSessionHas('success');
        $this->assertEquals('approved', $this->comment->fresh()->status);

        // Spam
        $spamResponse = $this->actingAs($this->admin)->post(route('admin.comments.spam', $this->comment->id));
        $spamResponse->assertRedirect();
        $spamResponse->assertSessionHas('success');
        $this->assertEquals('spam', $this->comment->fresh()->status);
    }

    public function test_comment_destroy_soft_deletes_record(): void {
        $response = $this->actingAs($this->admin)->delete(route('admin.comments.destroy', $this->comment->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('comments', ['id' => $this->comment->id]);
    }

    public function test_comment_restore_and_force_delete(): void {
        $this->comment->delete();

        // Check trash tab
        $trashView = $this->actingAs($this->admin)->get(route('admin.comments.index', ['status' => 'trash']));
        $trashView->assertStatus(200);
        $trashView->assertSee('Camille Desmoulins');

        // Restore
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.comments.restore', $this->comment->id));
        $restoreResponse->assertRedirect();
        $restoreResponse->assertSessionHas('success');
        $this->assertFalse($this->comment->fresh()->trashed());

        // Soft-delete again then force delete
        $this->comment->delete();
        $forceResponse = $this->actingAs($this->admin)->delete(route('admin.comments.forceDelete', $this->comment->id));
        $forceResponse->assertRedirect();
        $forceResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('comments', ['id' => $this->comment->id]);
    }

    public function test_reply_lifecycle(): void {
        $reply = CommentReply::create([
            'comment_id' => $this->comment->id,
            'user_id'    => $this->admin->id,
            'reply'      => 'We appreciate your thoughtful reflection.',
        ]);

        // Edit reply screen
        $editResponse = $this->actingAs($this->admin)->get(route('admin.comments.reply.edit', $reply->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Thread Reply');
        $editResponse->assertSee('We appreciate your thoughtful reflection.');

        // Update reply
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.comments.reply.update', $reply->id), [
            'reply' => 'Updated staff endorsement.',
        ]);
        $updateResponse->assertRedirect(route('admin.comments.index'));
        $this->assertEquals('Updated staff endorsement.', $reply->fresh()->reply);

        // Soft delete reply
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.comments.reply.destroy', $reply->id));
        $deleteResponse->assertRedirect();
        $this->assertSoftDeleted('comment_replies', ['id' => $reply->id]);

        // Restore reply
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.comments.reply.restore', $reply->id));
        $restoreResponse->assertRedirect();
        $this->assertFalse($reply->fresh()->trashed());

        // Force delete reply
        $reply->delete();
        $forceDelete = $this->actingAs($this->admin)->delete(route('admin.comments.reply.forceDelete', $reply->id));
        $forceDelete->assertRedirect();
        $this->assertDatabaseMissing('comment_replies', ['id' => $reply->id]);
    }

    public function test_bulk_actions_on_comments(): void {
        $c1 = Comment::create([
            'post_id'      => $this->post->id,
            'author_name'  => 'Reviewer 1',
            'author_email' => 'r1@xivig.com',
            'content'      => 'Remark one',
            'status'       => 'pending',
        ]);
        $c2 = Comment::create([
            'post_id'      => $this->post->id,
            'author_name'  => 'Reviewer 2',
            'author_email' => 'r2@xivig.com',
            'content'      => 'Remark two',
            'status'       => 'pending',
        ]);

        // Bulk Approve
        $bulkApprove = $this->actingAs($this->admin)->post(route('admin.comments.bulk'), [
            'action' => 'approve',
            'ids'    => [$c1->id, $c2->id],
        ]);
        $bulkApprove->assertRedirect();
        $this->assertEquals('approved', $c1->fresh()->status);
        $this->assertEquals('approved', $c2->fresh()->status);

        // Bulk Delete
        $bulkDelete = $this->actingAs($this->admin)->post(route('admin.comments.bulk'), [
            'action' => 'delete',
            'ids'    => [$c1->id, $c2->id],
        ]);
        $bulkDelete->assertRedirect();
        $this->assertSoftDeleted('comments', ['id' => $c1->id]);
        $this->assertSoftDeleted('comments', ['id' => $c2->id]);

        // Bulk Restore
        $bulkRestore = $this->actingAs($this->admin)->post(route('admin.comments.bulk'), [
            'action' => 'restore',
            'ids'    => [$c1->id, $c2->id],
        ]);
        $bulkRestore->assertRedirect();
        $this->assertFalse($c1->fresh()->trashed());
        $this->assertFalse($c2->fresh()->trashed());
    }
}
