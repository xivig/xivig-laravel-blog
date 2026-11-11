<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminEmail extends Model {
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_name',
        'sender_email',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'folder',
        'is_read',
        'is_starred',
        'is_archived',
        'attachment_path',
        'attachment_name',
        'reply_to_id',
        'ip_address',
    ];

    protected $casts = [
        'is_read'     => 'boolean',
        'is_starred'  => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function sender(): BelongsTo {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo(): BelongsTo {
        return $this->belongsTo(AdminEmail::class, 'reply_to_id');
    }

    public function replies(): HasMany {
        return $this->hasMany(AdminEmail::class, 'reply_to_id')->orderBy('created_at', 'asc');
    }

    public function scopeFolder($query, string $folder) {
        return $query->where('folder', $folder);
    }

    public function scopeStarred($query) {
        return $query->where('is_starred', true)->where('folder', '!=', 'trash');
    }

    public function scopeUnread($query) {
        return $query->where('is_read', false);
    }

    public function scopeSearch($query, ?string $q) {
        if (empty($q)) {
            return $query;
        }

        return $query->where(function ($sub) use ($q) {
            $sub->where('subject', 'like', "%{$q}%")
                ->orWhere('body', 'like', "%{$q}%")
                ->orWhere('sender_name', 'like', "%{$q}%")
                ->orWhere('sender_email', 'like', "%{$q}%")
                ->orWhere('recipient_email', 'like', "%{$q}%");
        });
    }

    public function formattedTime($user = null): string {
        $tz = $user?->timezone ?? Setting::get('timezone', config('app.timezone', 'UTC'));
        return $this->created_at->copy()->setTimezone($tz)->format('M d, Y h:i A');
    }
}
