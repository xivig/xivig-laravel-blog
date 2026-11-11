<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model {
    protected $fillable = [
        'title',
        'start_time',
        'end_time',
        'is_all_day',
        'color',
        'user_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'is_all_day' => 'boolean',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
