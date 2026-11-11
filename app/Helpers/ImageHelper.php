<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ImageHelper {
    /**
     * Generate responsive srcset string for luxury performance.
     */
    public static function generateSrcset(string $imagePath): string {
        $url = asset('storage/' . ltrim($imagePath, '/'));
        return "{$url} 1200w, {$url} 800w, {$url} 400w";
    }

    /**
     * Automatic semantic alt generator from title/context.
     */
    public static function generateAlt(string $context, ?string $explicit = null): string {
        if (!empty($explicit)) {
            return e($explicit);
        }
        return e(Str::title(str_replace('-', ' ', Str::slug($context))) . ' - Haute Luxury Chronicle Showcase');
    }

    /**
     * Return WebP version if exists or fallback.
     */
    public static function webpUrl(string $imagePath): string {
        $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imagePath);
        if (file_exists(public_path('storage/' . $webpPath))) {
            return asset('storage/' . $webpPath);
        }
        return asset('storage/' . ltrim($imagePath, '/'));
    }
}
