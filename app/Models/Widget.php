<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model {
    protected $fillable = [
        'name',
        'type',
        'area',
        'title',
        'settings',
        'content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'settings'  => 'array',
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function scopeActive($query) {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function scopeArea($query, string $area) {
        return $query->where('area', $area);
    }
}
