<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model {
    protected $fillable = [
        'title',
        'slot',
        'image_path',
        'target_url',
        'custom_code',
        'impressions',
        'clicks',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'impressions' => 'integer',
        'clicks'      => 'integer',
    ];

    public function recordImpression(): void {
        $this->increment('impressions');
    }

    public function recordClick(): void {
        $this->increment('clicks');
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeActiveSlot($query, string $slot) {
        return $query->where('is_active', true)->where('slot', $slot);
    }

    public function getImageUrlAttribute(): ?string {
        if (empty($this->image_path)) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        $clean = ltrim($this->image_path, '/');
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

    public function getIsClickableAttribute(): bool {
        return !empty($this->target_url);
    }
}
