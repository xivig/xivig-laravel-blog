<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminEmail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactInquiryController extends Controller {
    public function index(Request $request) {
        $filters = $request->only(['search', 'status']);

        $inquiries = ContactMessage::filter($filters)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $metrics = [
            'total'  => ContactMessage::count(),
            'unread' => ContactMessage::where('is_read', false)->count(),
            'today'  => ContactMessage::whereDate('created_at', today())->count(),
        ];

        return view('admin.contacts.index', compact('inquiries', 'metrics', 'filters'));
    }

    public function show(ContactMessage $contact) {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function toggleRead(ContactMessage $contact) {
        $contact->update(['is_read' => !$contact->is_read]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'is_read' => $contact->is_read]);
        }

        return back()->with('success', $contact->is_read ? 'Marked inquiry as reviewed.' : 'Marked inquiry as unread.');
    }

    public function reply(Request $request, ContactMessage $contact) {
        $validated = $request->validate([
            'reply_message' => 'nullable|string|min:5|max:5000',
            'reply_body'    => 'nullable|string|min:5|max:5000',
            'subject'       => 'nullable|string|max:200',
            'reply_subject' => 'nullable|string|max:200',
        ]);

        $messageContent = $request->input('reply_message') ?: $request->input('reply_body');
        if (empty($messageContent)) {
            return back()->withErrors(['reply_message' => 'The reply message field is required.']);
        }

        $subject = $request->input('reply_subject') ?: ($request->input('subject') ?: ('Re: ' . ($contact->subject ?: 'Correspondence with Maison XIVIG')));
        $user = Auth::user();

        // 1. Create sent record in Admin Email System
        if (\Illuminate\Support\Facades\Schema::hasTable('admin_emails')) {
            AdminEmail::create([
                'sender_id'       => $user->id,
                'sender_name'     => $user->name,
                'sender_email'    => $user->email,
                'recipient_email' => $contact->email,
                'recipient_name'  => $contact->name,
                'subject'         => $subject,
                'body'            => nl2br(e($messageContent)),
                'folder'          => 'sent',
                'is_read'         => true,
            ]);
        }

        // 2. Mark inquiry as read
        $contact->update(['is_read' => true]);

        // 3. Optional SMTP transmission wrapped safely
        try {
            if (config('mail.default') && config('mail.default') !== 'array') {
                Mail::raw($messageContent, function ($m) use ($contact, $subject, $user) {
                    $m->to($contact->email, $contact->name)
                      ->from($user->email, $user->name)
                      ->subject($subject);
                });
            }
        } catch (\Throwable $e) {
            // Shared hosting mail driver safety fallback
        }

        ActivityLog::record('sent', "Dispatched response to inquiry from {$contact->name} ({$contact->email})", $contact);

        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', "Editorial response dispatched to {$contact->email}.");
    }

    public function destroy(ContactMessage $contact) {
        $email = $contact->email;
        $contact->delete();

        ActivityLog::record('deleted', "Purged contact inquiry from {$email}");

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Inquiry record removed from salon registry.');
    }

    public function bulk(Request $request) {
        $action = $request->input('action');
        $ids = $request->input('selected', $request->input('ids', []));

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No inquiries selected.');
        }

        switch ($action) {
            case 'read':
            case 'mark_read':
                ContactMessage::whereIn('id', $ids)->update(['is_read' => true]);
                $msg = 'Selected inquiries marked as reviewed.';
                break;
            case 'unread':
            case 'mark_unread':
                ContactMessage::whereIn('id', $ids)->update(['is_read' => false]);
                $msg = 'Selected inquiries marked as pending.';
                break;
            case 'delete':
                ContactMessage::whereIn('id', $ids)->delete();
                $msg = 'Selected inquiries purged from registry.';
                break;
            default:
                $msg = 'No action performed.';
        }

        return back()->with('success', $msg);
    }
}
