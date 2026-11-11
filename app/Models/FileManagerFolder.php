<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileManagerFolder extends Model {
    use SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'color',
    ];

    public function subfolders(): HasMany {
        return $this->hasMany(FileManagerFolder::class, 'parent_id');
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(FileManagerFolder::class, 'parent_id');
    }

    public function files(): HasMany {
        return $this->hasMany(FileManagerFile::class, 'folder_id');
    }
}
