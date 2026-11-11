<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model {
    use HasFactory;

    protected $fillable = [
        'gallery_id',
        'image_path',
        'title',
        'caption',
        'order',
    ];

    public function gallery(): BelongsTo {
        return $this->belongsTo(Gallery::class);
    }

    public function getIsCoverAttribute(): bool {
        return $this->gallery && $this->gallery->cover_image === $this->image_path;
    }
}
