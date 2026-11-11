<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\HasSeo;
use App\Traits\HasSlug;
use App\Traits\TrackActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model {
    use HasFactory, SoftDeletes, HasSlug, HasSeo, TrackActivity, Filterable;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'template',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished($query) {
        return $query->where('is_published', true);
    }
}
