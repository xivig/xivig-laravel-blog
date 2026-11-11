<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileManagerFile extends Model {
    use SoftDeletes;

    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
        'icon',
        'color',
        'folder_id',
    ];

    public function folder(): BelongsTo {
        return $this->belongsTo(FileManagerFolder::class, 'folder_id');
    }

    public function getUrlAttribute(): string {
        return asset($this->path);
    }
}
