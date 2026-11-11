<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaAsset extends Model {
    use SoftDeletes;

    protected $fillable = [
        'original_name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'dimensions',
        'alt_text',
    ];

    public function getUrlAttribute(): string {
        $clean = ltrim($this->file_path, '/');
        $relativePath = str_starts_with($clean, 'storage/') ? substr($clean, 8) : $clean;

        $publicFile = public_path('storage/' . $relativePath);
        $storageFile = storage_path('app/public/' . $relativePath);

        if (!file_exists($publicFile) && file_exists($storageFile)) {
            $dir = dirname($publicFile);
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            @copy($storageFile, $publicFile);
        }

        return '/storage/' . $relativePath;
    }

    public function getFormattedSizeAttribute(): string {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) return '0 KB';
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / (1024 * 1024), 2) . ' MB';
    }

    public function getExtensionAttribute(): string {
        return strtoupper(pathinfo($this->file_path, PATHINFO_EXTENSION) ?: 'IMG');
    }

    public function getDimensionsLabelAttribute(): ?string {
        if (empty($this->dimensions)) return null;
        return str_replace('x', ' × ', $this->dimensions) . ' px';
    }

    public function scopeSearch($query, ?string $term) {
        if (empty($term)) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('original_name', 'like', "%{$term}%")
              ->orWhere('alt_text', 'like', "%{$term}%")
              ->orWhere('file_name', 'like', "%{$term}%");
        });
    }

    public function scopeOfType($query, ?string $type) {
        if (empty($type) || $type === 'all') return $query;
        return $query->where('mime_type', 'like', "%{$type}%");
    }
}
