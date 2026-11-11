<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model {
    protected $fillable = [
        'ip_address',
        'session_id',
        'device',
        'browser',
        'country_code',
        'country_name',
        'city',
        'user_agent',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at'  => 'datetime',
    ];

    public function pageViews(): HasMany {
        return $this->hasMany(PageView::class);
    }
}
