<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'email',
        'avatar',
        'bio',
        'designation',
        'twitter',
        'linkedin',
        'instagram',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts() {
        return $this->hasMany(Post::class)->where('status', 'published');
    }

    public function getAvatarUrlAttribute(): ?string {
        $avatar = $this->avatar;
        if (empty($avatar) && $this->user) {
            $avatar = $this->user->avatar;
        }

        if (empty($avatar)) {
            return null;
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        $clean = ltrim($avatar, '/');
        $relativePath = str_starts_with($clean, 'storage/') ? substr($clean, 8) : $clean;

        // Auto-mirror to public/storage if missing (handles Windows non-symlink environments)
        $publicFile = public_path('storage/' . $relativePath);
        $storageFile = storage_path('app/public/' . $relativePath);

        if (!file_exists($publicFile) && file_exists($storageFile)) {
            $dir = dirname($publicFile);
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            @copy($storageFile, $publicFile);
        }

        if (!file_exists($publicFile) && !file_exists($storageFile)) {
            return null;
        }

        return '/storage/' . $relativePath;
    }
}
