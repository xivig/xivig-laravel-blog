<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model {
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'channel',
        'message',
        'attachment',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function sender(): BelongsTo {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopeForChannel($query, string $channel) {
        return $query->where('channel', $channel)->whereNull('recipient_id');
    }

    public function scopeBetweenUsers($query, int $userA, int $userB) {
        return $query->where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('recipient_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB)->where('recipient_id', $userA);
        });
    }

    public function scopeUnreadFor($query, int $userId) {
        return $query->where('recipient_id', $userId)->where('is_read', false);
    }
}
