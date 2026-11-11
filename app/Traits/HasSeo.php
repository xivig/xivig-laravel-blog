<?php

namespace App\Traits;

use App\Models\SEO;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo {
    public function seo(): MorphOne {
        return $this->morphOne(SEO::class, 'seoable');
    }

    public function getEffectiveMetaTitleAttribute(): string {
        return $this->seo?->meta_title ?: $this->meta_title ?: $this->title ?: config('app.name');
    }

    public function getEffectiveMetaDescriptionAttribute(): string {
        return $this->seo?->meta_description ?: $this->meta_description ?: ($this->excerpt ?? '');
    }

    public function getEffectiveCanonicalUrlAttribute(): string {
        return $this->seo?->canonical_url ?: url()->current();
    }
}
