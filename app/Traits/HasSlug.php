<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug {
    public static function bootHasSlug(): void {
        static::creating(function ($model) {
            $slugField = $model->slugField ?? 'title';
            if (empty($model->slug) && !empty($model->{$slugField})) {
                $baseSlug = Str::slug($model->{$slugField});
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $model->slug = $slug;
            }
        });
    }
}
