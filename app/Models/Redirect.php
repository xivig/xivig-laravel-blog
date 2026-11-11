<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model {
    protected $fillable = [
        'source_url',
        'target_url',
        'status_code',
        'hits',
        'is_active',
        'last_accessed_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'hits' => 'integer',
        'is_active' => 'boolean',
        'last_accessed_at' => 'datetime',
    ];

    public function recordHit(): void {
        $this->increment('hits');
        $this->update(['last_accessed_at' => now()]);
    }
}
