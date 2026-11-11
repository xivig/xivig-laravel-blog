<?php

namespace App\Services;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureUploadService {
    /**
     * Whitelist of strictly permitted MIME types and extensions for security.
     */
    protected static array $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
    ];

    protected static int $maxSizeBytes = 15728640; // 15 MB (synchronized with Controller validation)

    /**
     * Validate and securely store an uploaded image asset.
     */
    public static function upload(UploadedFile $file, ?string $altText = null): MediaAsset {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Uploaded file is corrupted or incomplete.');
        }

        if ($file->getSize() > self::$maxSizeBytes) {
            throw new \InvalidArgumentException('File exceeds the maximum permitted size of 15MB.');
        }

        $mime = $file->getMimeType();
        if (!array_key_exists($mime, self::$allowedMimes)) {
            throw new \InvalidArgumentException('Invalid image format. Only JPG, PNG, WEBP, and AVIF are permitted.');
        }

        // Verify client extension matches permitted MIME extension
        $clientExt = strtolower($file->getClientOriginalExtension());
        $safeExt = self::$allowedMimes[$mime];
        if (!in_array($clientExt, ['jpg', 'jpeg', 'png', 'webp', 'avif'])) {
            throw new \InvalidArgumentException('File extension does not match authorized image types.');
        }

        // Generate cryptographically random filename (prevents directory traversal & file collision)
        $randomFileName = Str::random(40) . '.' . $safeExt;
        $path = $file->storeAs('media', $randomFileName, 'public');

        // Extract dimensions if available
        $dimensions = null;
        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $dimensions = $imageInfo[0] . 'x' . $imageInfo[1];
            }
        }

        // Optimize image and generate WebP companion if supported
        self::optimizeStoredAsset($path, $mime);

        return MediaAsset::create([
            'original_name' => strip_tags($file->getClientOriginalName()),
            'file_name'     => $randomFileName,
            'file_path'     => $path,
            'file_size'     => $file->getSize(),
            'mime_type'     => $mime,
            'dimensions'    => $dimensions,
            'alt_text'      => $altText ? strip_tags($altText) : null,
        ]);
    }

    /**
     * Lossless/high-fidelity image optimization and WebP companion generation.
     */
    protected static function optimizeStoredAsset(string $relativePath, string $mime): void {
        try {
            $fullPath = Storage::disk('public')->path($relativePath);
            if (!file_exists($fullPath) || !extension_loaded('gd')) {
                return;
            }

            if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg') && function_exists('imagewebp')) {
                $image = @imagecreatefromjpeg($fullPath);
                if ($image) {
                    $webpPath = preg_replace('/\.(jpe?g)$/i', '.webp', $fullPath);
                    if ($webpPath && $webpPath !== $fullPath) {
                        @imagewebp($image, $webpPath, 85);
                    }
                    @imagedestroy($image);
                }
            } elseif ($mime === 'image/png' && function_exists('imagecreatefrompng') && function_exists('imagewebp')) {
                $image = @imagecreatefrompng($fullPath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    $webpPath = preg_replace('/\.png$/i', '.webp', $fullPath);
                    if ($webpPath && $webpPath !== $fullPath) {
                        @imagewebp($image, $webpPath, 85);
                    }
                    @imagedestroy($image);
                }
            }
        } catch (\Throwable $e) {
            // Non-blocking: skip optimization if file cannot be read by GD
        }
    }
}
