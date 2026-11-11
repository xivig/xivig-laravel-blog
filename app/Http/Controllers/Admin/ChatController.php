<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TimezoneHelper;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller {
    public function index(Request $request) {
        $channels = [
            'editorial_salon' => [
                'name'        => 'Editorial Salon',
                'description' => 'Maison chief editors & active desk dialogue',
                'icon'        => 'bi-chat-heart-fill',
            ],
            'haute_couture' => [
                'name'        => 'Haute Couture & Runway',
                'description' => 'Paris & Milan atelier curation, textiles, & lookbooks',
                'icon'        => 'bi-palette-fill',
            ],
            'horology' => [
                'name'        => 'Horology & Complications',
                'description' => 'Geneva watchmakers, tourbillons & artisanal dials',
                'icon'        => 'bi-stopwatch-fill',
            ],
            'tech_desk' => [
                'name'        => 'Dispatch & Infrastructure',
                'description' => 'Platform operational notices, backups, and tech logs',
                'icon'        => 'bi-shield-shaded',
            ],
        ];

        $currentChannel = $request->query('channel');
        $recipientId = $request->query('recipient_id');

        if (!$currentChannel && !$recipientId) {
            $currentChannel = 'editorial_salon';
        }

        $currentUser = Auth::user();

        // Staff members for direct messages
        $staffUsers = User::where('id', '!=', $currentUser->id)
            ->orderBy('name')
            ->get()
            ->map(function ($staff) use ($currentUser) {
                $staff->unread_count = ChatMessage::where('sender_id', $staff->id)
                    ->where('recipient_id', $currentUser->id)
                    ->where('is_read', false)
                    ->count();
                return $staff;
            });

        $activeRecipient = $recipientId ? User::find($recipientId) : null;

        // Fetch initial messages
        $messagesQuery = ChatMessage::with('sender');
        if ($activeRecipient) {
            $messagesQuery->betweenUsers($currentUser->id, $activeRecipient->id);
            // Mark as read
            ChatMessage::where('sender_id', $activeRecipient->id)
                ->where('recipient_id', $currentUser->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            $messagesQuery->forChannel($currentChannel);
        }

        $initialMessages = $messagesQuery->latest()->take(50)->get()->reverse()->values();

        return view('admin.chat.index', compact(
            'channels',
            'currentChannel',
            'staffUsers',
            'activeRecipient',
            'initialMessages'
        ));
    }

    public function fetch(Request $request) {
        $currentUser = Auth::user();
        $channel = $request->query('channel');
        $recipientId = $request->query('recipient_id');
        $afterId = (int) $request->query('after_id', 0);

        $query = ChatMessage::with('sender');

        if (!empty($recipientId)) {
            $query->betweenUsers($currentUser->id, (int) $recipientId);
            // Mark unread from recipient as read
            ChatMessage::where('sender_id', (int) $recipientId)
                ->where('recipient_id', $currentUser->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            $query->forChannel($channel ?: 'editorial_salon');
        }

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        } else {
            $query->latest()->take(40);
        }

        $records = $query->orderBy('id', 'asc')->get();

        $messages = $records->map(function ($msg) use ($currentUser) {
            return [
                'id'             => $msg->id,
                'sender_id'      => $msg->sender_id,
                'sender_name'    => $msg->sender ? $msg->sender->name : 'Maison Contributor',
                'sender_avatar'  => $msg->sender?->avatar,
                'is_me'          => $msg->sender_id === $currentUser->id,
                'message'        => e($msg->message),
                'attachment_url' => $msg->attachment ? asset($msg->attachment) : null,
                'formatted_time' => TimezoneHelper::format($msg->created_at, 'h:i A', $currentUser),
                'formatted_date' => TimezoneHelper::format($msg->created_at, 'M d, Y', $currentUser),
            ];
        });

        $lastId = $records->last()?->id ?? $afterId;

        return response()->json([
            'messages' => $messages,
            'last_id'  => $lastId,
        ]);
    }

    public function send(Request $request) {
        $request->validate([
            'message'      => 'required_without:attachment|nullable|string|max:3000',
            'channel'      => 'nullable|string|max:100',
            'recipient_id' => 'nullable|exists:users,id',
            'attachment'   => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,pdf,zip|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'chat_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('chat_attachments', $filename, 'public');
            $attachmentPath = '/storage/' . $path;
        }

        $currentUser = Auth::user();

        $msg = ChatMessage::create([
            'sender_id'    => $currentUser->id,
            'recipient_id' => $request->recipient_id ?: null,
            'channel'      => $request->recipient_id ? null : ($request->channel ?: 'editorial_salon'),
            'message'      => $request->message ?? '',
            'attachment'   => $attachmentPath,
            'is_read'      => false,
        ]);

        $msg->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id'             => $msg->id,
                'sender_id'      => $msg->sender_id,
                'sender_name'    => $currentUser->name,
                'sender_avatar'  => $currentUser->avatar,
                'is_me'          => true,
                'message'        => e($msg->message),
                'attachment_url' => $attachmentPath ? asset($attachmentPath) : null,
                'formatted_time' => TimezoneHelper::format($msg->created_at, 'h:i A', $currentUser),
                'formatted_date' => TimezoneHelper::format($msg->created_at, 'M d, Y', $currentUser),
            ],
        ]);
    }
}
