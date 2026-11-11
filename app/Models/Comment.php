<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'author_name',
        'author_email',
        'author_website',
        'content',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected static function booted(): void {
        static::saving(function ($comment) {
            if (!empty($comment->content)) {
                $comment->content = \App\Services\HtmlSanitizerService::cleanComment($comment->content);
            }
        });
    }

    public function post(): BelongsTo {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function comment(): BelongsTo {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function getUserAttribute() {
        if ($this->relationLoaded('user') && $this->getRelation('user')) {
            return $this->getRelation('user');
        }
        if ($this->user_id && ($u = User::find($this->user_id))) {
            return $u;
        }
        return (object) [
            'id'              => null,
            'name'            => $this->author_name ?: 'Guest Reader',
            'email'           => $this->author_email ?: '',
            'role'            => 'user',
            'profile_picture' => null,
        ];
    }

    public function getCommentAttribute(): string {
        return (string) $this->content;
    }

    public function setCommentAttribute($value): void {
        $this->content = $value;
    }

    public function getReplyAttribute(): string {
        return (string) $this->content;
    }

    public function setReplyAttribute($value): void {
        $this->content = $value;
    }

    public function getCommentIdAttribute(): ?int {
        return $this->parent_id;
    }

    public function setCommentIdAttribute($value): void {
        $this->parent_id = $value;
    }

    public function replies(): HasMany {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    public function commentReplies(): HasMany {
        return $this->hasMany(CommentReply::class);
    }
}
