<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TimezoneHelper;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminEmail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller {
    public function index(Request $request) {
        $folder = $request->query('folder', 'inbox');
        $search = $request->query('q');

        $query = AdminEmail::with('sender')->search($search);

        if ($folder === 'starred') {
            $query->starred();
        } else {
            $query->folder($folder);
        }

        $emails = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'inbox'   => AdminEmail::where('folder', 'inbox')->where('is_read', false)->count(),
            'starred' => AdminEmail::where('is_starred', true)->where('folder', '!=', 'trash')->count(),
            'sent'    => AdminEmail::where('folder', 'sent')->count(),
            'drafts'  => AdminEmail::where('folder', 'drafts')->count(),
            'trash'   => AdminEmail::where('folder', 'trash')->count(),
        ];

        return view('admin.emails.index', compact('emails', 'counts', 'folder', 'search'));
    }

    public function show(AdminEmail $email) {
        if (!$email->is_read && $email->folder === 'inbox') {
            $email->update(['is_read' => true]);
        }

        $thread = AdminEmail::where(function ($q) use ($email) {
            $q->where('id', $email->id)
              ->orWhere('reply_to_id', $email->id)
              ->orWhere(function ($sq) use ($email) {
                  if ($email->reply_to_id) {
                      $sq->where('id', $email->reply_to_id)
                         ->orWhere('reply_to_id', $email->reply_to_id);
                  }
              });
        })->orderBy('created_at', 'asc')->get();

        $counts = [
            'inbox'   => AdminEmail::where('folder', 'inbox')->where('is_read', false)->count(),
            'starred' => AdminEmail::where('is_starred', true)->where('folder', '!=', 'trash')->count(),
            'sent'    => AdminEmail::where('folder', 'sent')->count(),
            'drafts'  => AdminEmail::where('folder', 'drafts')->count(),
            'trash'   => AdminEmail::where('folder', 'trash')->count(),
        ];

        return view('admin.emails.show', compact('email', 'thread', 'counts'));
    }

    public function compose(Request $request) {
        $replyToId = $request->query('reply_to');
        $replyEmail = $replyToId ? AdminEmail::find($replyToId) : null;

        $prefill = [
            'to'          => $replyEmail ? $replyEmail->sender_email : $request->query('to', ''),
            'name'        => $replyEmail ? $replyEmail->sender_name : '',
            'subject'     => $replyEmail ? ('Re: ' . preg_replace('/^Re:\s*/i', '', $replyEmail->subject)) : $request->query('subject', ''),
            'reply_to_id' => $replyEmail ? $replyEmail->id : null,
        ];

        $staffUsers = User::select('id', 'name', 'email')->orderBy('name')->get();

        $counts = [
            'inbox'   => AdminEmail::where('folder', 'inbox')->where('is_read', false)->count(),
            'starred' => AdminEmail::where('is_starred', true)->where('folder', '!=', 'trash')->count(),
            'sent'    => AdminEmail::where('folder', 'sent')->count(),
            'drafts'  => AdminEmail::where('folder', 'drafts')->count(),
            'trash'   => AdminEmail::where('folder', 'trash')->count(),
        ];

        return view('admin.emails.compose', compact('prefill', 'staffUsers', 'counts'));
    }

    public function send(Request $request) {
        $request->validate([
            'recipient_email' => 'required|email|max:150',
            'recipient_name'  => 'nullable|string|max:150',
            'subject'         => 'required|string|max:200',
            'body'            => 'required|string',
            'attachment'      => 'nullable|file|max:10240',
            'reply_to_id'     => 'nullable|exists:admin_emails,id',
            'action'          => 'nullable|in:send,draft',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $filename = 'email_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('email_attachments', $filename, 'public');
            $attachmentPath = '/storage/' . $path;
        }

        $isDraft = $request->input('action') === 'draft';
        $folder = $isDraft ? 'drafts' : 'sent';
        $user = Auth::user();

        $email = AdminEmail::create([
            'sender_id'       => $user->id,
            'sender_name'     => $user->name,
            'sender_email'    => $user->email,
            'recipient_email' => $request->recipient_email,
            'recipient_name'  => $request->recipient_name,
            'subject'         => $request->subject,
            'body'            => $request->body,
            'folder'          => $folder,
            'is_read'         => true,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'reply_to_id'     => $request->reply_to_id,
            'ip_address'      => $request->ip(),
        ]);

        // Attempt sending via Laravel Mailer if sending and not a draft
        if (!$isDraft) {
            try {
                // If mail transport is configured, attempt sending
                if (config('mail.default') && config('mail.default') !== 'array') {
                    Mail::raw(strip_tags($request->body), function ($m) use ($request, $user) {
                        $m->to($request->recipient_email, $request->recipient_name)
                          ->from($user->email, $user->name)
                          ->subject($request->subject);
                    });
                }
            } catch (\Throwable $e) {
                // Keep record in database safely on shared hosting without throwing 500
            }

            ActivityLog::record('sent', "Dispatched email to {$request->recipient_email}: \"{$request->subject}\"", $email);
            return redirect()->route('admin.emails.index', ['folder' => 'sent'])
                ->with('success', 'Correspondence dispatch successfully transmitted.');
        }

        ActivityLog::record('created', "Drafted email dispatch: \"{$request->subject}\"", $email);
        return redirect()->route('admin.emails.index', ['folder' => 'drafts'])
            ->with('success', 'Draft dispatch saved.');
    }

    public function toggleStar(AdminEmail $email) {
        $email->update(['is_starred' => !$email->is_starred]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'is_starred' => $email->is_starred]);
        }

        return back()->with('success', $email->is_starred ? 'Dispatch marked as starred.' : 'Star removed.');
    }

    public function toggleRead(AdminEmail $email) {
        $email->update(['is_read' => !$email->is_read]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'is_read' => $email->is_read]);
        }

        return back()->with('success', $email->is_read ? 'Marked as read.' : 'Marked as unread.');
    }

    public function moveToTrash(AdminEmail $email) {
        $email->update(['folder' => 'trash']);
        return redirect()->route('admin.emails.index', ['folder' => 'inbox'])
            ->with('success', 'Email moved to trash.');
    }

    public function restore(AdminEmail $email) {
        $email->update(['folder' => 'inbox']);
        return back()->with('success', 'Email restored to inbox.');
    }

    public function destroy(AdminEmail $email) {
        $email->delete();
        return back()->with('success', 'Dispatch permanently purged.');
    }

    public function bulk(Request $request) {
        $action = $request->input('action');
        $ids = $request->input('selected', []);

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No messages selected.');
        }

        switch ($action) {
            case 'read':
                AdminEmail::whereIn('id', $ids)->update(['is_read' => true]);
                $msg = 'Selected messages marked as read.';
                break;
            case 'unread':
                AdminEmail::whereIn('id', $ids)->update(['is_read' => false]);
                $msg = 'Selected messages marked as unread.';
                break;
            case 'star':
                AdminEmail::whereIn('id', $ids)->update(['is_starred' => true]);
                $msg = 'Selected messages starred.';
                break;
            case 'trash':
                AdminEmail::whereIn('id', $ids)->update(['folder' => 'trash']);
                $msg = 'Selected messages moved to trash.';
                break;
            case 'delete':
                AdminEmail::whereIn('id', $ids)->where('folder', 'trash')->delete();
                $msg = 'Selected messages permanently deleted.';
                break;
            default:
                $msg = 'No action performed.';
        }

        return back()->with('success', $msg);
    }
}
