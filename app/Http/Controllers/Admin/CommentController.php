<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Post;
use App\Models\User;
use App\Services\HtmlSanitizerService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CommentController extends Controller {

    public function index(Request $request) {
        $status = $request->get('status');

        $commentQuery = Comment::with(['user', 'post', 'parent']);
        $replyQuery = CommentReply::with(['user', 'comment.post']);

        if ($status === 'trash') {
            $commentQuery->onlyTrashed();
            $replyQuery->onlyTrashed();
        } else {
            if ($request->filled('status')) {
                $commentQuery->where('status', $status);
            }
        }

        if ($request->filled('search')) {
            $term = trim($request->search);
            $commentQuery->where(function ($q) use ($term) {
                $q->where('content', 'like', "%{$term}%")
                  ->orWhere('author_name', 'like', "%{$term}%")
                  ->orWhere('author_email', 'like', "%{$term}%");
            });
            $replyQuery->where(function ($q) use ($term) {
                $q->where('reply', 'like', "%{$term}%");
            });
        }

        $comments = $commentQuery->latest()->get();
        $replies = $replyQuery->latest()->get();

        // Merge comments and replies
        $allActivity = $comments->concat($replies)->sortByDesc('created_at');

        // Paginate the merged collection
        $currentPage = Paginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $allActivity->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $allActivity = new LengthAwarePaginator(
            $currentItems,
            $allActivity->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $trashedCount = Comment::onlyTrashed()->count() + CommentReply::onlyTrashed()->count();
        $totalCount = Comment::count();
        $approvedCount = Comment::where('status', 'approved')->count();
        $pendingCount = Comment::where('status', 'pending')->count();
        $spamCount = Comment::where('status', 'spam')->count();

        return view('admin_crud_all.comment.comment', compact(
            'allActivity',
            'trashedCount',
            'totalCount',
            'approvedCount',
            'pendingCount',
            'spamCount'
        ));
    }

    public function create() {
        $posts = Post::whereIn('status', ['published', '1'])->latest()->get();
        $users = User::orderBy('name')->get();

        return view('admin_crud_all.comment.comment-add', compact('posts', 'users'));
    }

    public function store(Request $request) {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'comment' => 'required|string|max:2000',
        ]);

        $user = User::findOrFail($request->user_id);

        $cleanComment = HtmlSanitizerService::cleanComment($request->comment);

        $comment = Comment::create([
            'post_id'      => $request->post_id,
            'user_id'      => $user->id,
            'author_name'  => $user->name,
            'author_email' => $user->email,
            'content'      => $cleanComment,
            'status'       => 'approved',
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        ActivityLog::record('create_comment', "Added comment ID #{$comment->id} on post #{$comment->post_id}", $comment);

        return redirect()->route('admin.comments.index')->with('success', 'Comment added successfully.');
    }

    public function show(Comment $comment) {
        $comment->load(['user', 'post', 'replies.user']);

        return view('admin_crud_all.comment.comment-show', compact('comment'));
    }

    public function edit(Comment $comment) {
        $posts = Post::latest()->get();

        return view('admin_crud_all.comment.comment-edit', compact('comment', 'posts'));
    }

    public function update(Request $request, Comment $comment) {
        $request->validate([
            'comment' => 'required|string|max:2000',
            'post_id' => 'required|exists:posts,id',
            'status'  => 'nullable|in:approved,pending,spam',
        ]);

        $comment->content = HtmlSanitizerService::cleanComment($request->comment);
        $comment->post_id = $request->post_id;
        if ($request->filled('status')) {
            $comment->status = $request->status;
        }
        $comment->save();

        ActivityLog::record('edit_comment', "Updated comment ID #{$comment->id}", $comment);

        return redirect()->route('admin.comments.index')->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment) {
        $id = $comment->id;
        $comment->delete();

        ActivityLog::record('delete_comment', "Moved comment #{$id} to trash", $comment);

        return back()->with('success', 'Comment moved to trash.');
    }

    public function restore($id) {
        $comment = Comment::onlyTrashed()->find($id);
        if ($comment) {
            $comment->restore();
            ActivityLog::record('restore_comment', "Restored comment #{$id}", $comment);
            return back()->with('success', 'Comment restored.');
        }

        $reply = CommentReply::onlyTrashed()->findOrFail($id);
        $reply->restore();
        ActivityLog::record('restore_reply', "Restored reply #{$id}", $reply);

        return back()->with('success', 'Reply restored.');
    }

    public function forceDelete($id) {
        $comment = Comment::onlyTrashed()->find($id);
        if ($comment) {
            $comment->forceDelete();
            ActivityLog::record('force_delete_comment', "Permanently deleted comment #{$id}");
            return back()->with('success', 'Comment permanently removed.');
        }

        $reply = CommentReply::onlyTrashed()->findOrFail($id);
        $reply->forceDelete();
        ActivityLog::record('force_delete_reply', "Permanently deleted reply #{$id}");

        return back()->with('success', 'Reply permanently removed.');
    }

    public function approve(Comment $comment) {
        $comment->update(['status' => 'approved']);
        ActivityLog::record('approve_comment', "Approved comment ID #{$comment->id}", $comment);

        return back()->with('success', 'Comment approved.');
    }

    public function spam(Comment $comment) {
        $comment->update(['status' => 'spam']);
        ActivityLog::record('spam_comment', "Marked comment #{$comment->id} as spam", $comment);

        return back()->with('success', 'Comment marked as spam.');
    }

    // --- REPLIES ---

    public function editReply($id) {
        $reply = CommentReply::with('comment.user')->find($id);
        if (!$reply) {
            $reply = Comment::whereNotNull('parent_id')->with('parent.user')->findOrFail($id);
        }

        return view('admin_crud_all.comment.reply-edit', compact('reply'));
    }

    public function updateReply(Request $request, $id) {
        $request->validate([
            'reply' => 'required|string|max:2000',
        ]);

        $cleanReply = HtmlSanitizerService::cleanComment($request->reply);

        $reply = CommentReply::find($id);
        if ($reply) {
            $reply->update(['reply' => $cleanReply]);
            ActivityLog::record('edit_reply', "Updated reply ID #{$reply->id}", $reply);
        } else {
            $comment = Comment::whereNotNull('parent_id')->findOrFail($id);
            $comment->update(['content' => $cleanReply]);
            ActivityLog::record('edit_reply', "Updated nested reply comment #{$comment->id}", $comment);
        }

        return redirect()->route('admin.comments.index')->with('success', 'Reply updated successfully.');
    }

    public function destroyReply($id) {
        $reply = CommentReply::find($id);
        if ($reply) {
            $reply->delete();
            ActivityLog::record('delete_reply', "Moved reply #{$id} to trash", $reply);
        } else {
            $comment = Comment::whereNotNull('parent_id')->findOrFail($id);
            $comment->delete();
            ActivityLog::record('delete_reply', "Moved reply comment #{$id} to trash", $comment);
        }

        return back()->with('success', 'Reply moved to trash.');
    }

    public function restoreReply($id) {
        return $this->restore($id);
    }

    public function forceDeleteReply($id) {
        return $this->forceDelete($id);
    }

    public function bulkAction(Request $request) {
        $ids = $request->ids;
        $action = $request->action;

        if (!$ids || !$action) {
            return back()->with('error', 'Invalid bulk action specification.');
        }

        if ($action === 'delete') {
            Comment::whereIn('id', $ids)->delete();
            CommentReply::whereIn('id', $ids)->delete();
            ActivityLog::record('bulk_delete_comments', 'Moved ' . count($ids) . ' comments/replies to trash');
            return back()->with('success', 'Selected items moved to trash.');
        }

        if ($action === 'restore') {
            Comment::onlyTrashed()->whereIn('id', $ids)->restore();
            CommentReply::onlyTrashed()->whereIn('id', $ids)->restore();
            ActivityLog::record('bulk_restore_comments', 'Restored ' . count($ids) . ' comments/replies');
            return back()->with('success', 'Selected items restored successfully.');
        }

        if ($action === 'force_delete') {
            Comment::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            CommentReply::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            ActivityLog::record('bulk_force_delete_comments', 'Permanently deleted ' . count($ids) . ' comments/replies');
            return back()->with('success', 'Selected items permanently deleted.');
        }

        if ($action === 'approve') {
            Comment::whereIn('id', $ids)->update(['status' => 'approved']);
            ActivityLog::record('bulk_approve_comments', 'Approved ' . count($ids) . ' comments');
            return back()->with('success', 'Selected comments approved.');
        }

        if ($action === 'spam') {
            Comment::whereIn('id', $ids)->update(['status' => 'spam']);
            ActivityLog::record('bulk_spam_comments', 'Marked ' . count($ids) . ' comments as spam');
            return back()->with('success', 'Selected comments marked as spam.');
        }

        return back();
    }
}
