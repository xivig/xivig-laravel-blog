<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Revision extends Model {
    protected $fillable = [
        'user_id',
        'revisionable_type',
        'revisionable_id',
        'title',
        'content',
        'summary',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function revisionable(): MorphTo {
        return $this->morphTo();
    }
}
