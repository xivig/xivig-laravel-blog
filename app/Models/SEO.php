<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SEO extends Model {
    protected $table = 'seos';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'schema_type',
        'schema_custom',
    ];

    protected $casts = [
        'schema_custom' => 'array',
    ];

    public function seoable(): MorphTo {
        return $this->morphTo();
    }
}
