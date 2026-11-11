<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PageView extends Model {
    protected $fillable = [
        'visitor_id',
        'url',
        'path',
        'viewable_type',
        'viewable_id',
        'referrer',
        'ip_address',
    ];

    public function visitor(): BelongsTo {
        return $this->belongsTo(Visitor::class);
    }

    public function viewable(): MorphTo {
        return $this->morphTo();
    }
}
