<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model {
    protected $fillable = ['name', 'location', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items(): HasMany {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    public function rootItems(): HasMany {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }

    public static function forLocation(string $location): ?self {
        return static::with(['items.children'])->where('location', $location)->where('is_active', true)->first();
    }
}
