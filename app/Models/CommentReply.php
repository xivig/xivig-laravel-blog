<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentReply extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comment_id',
        'user_id',
        'reply',
    ];

    protected static function booted(): void {
        static::saving(function ($reply) {
            if (!empty($reply->reply)) {
                $reply->reply = \App\Services\HtmlSanitizerService::cleanComment($reply->reply);
            }
        });
    }

    public function comment(): BelongsTo {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
