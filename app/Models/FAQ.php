<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model {
    protected $table = 'faqs';

    protected $fillable = ['question', 'answer', 'category', 'order', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
        'order'        => 'integer',
    ];

    public function scopePublished($query) {
        return $query->where('is_published', true)->orderBy('order');
    }
}
