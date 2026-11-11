<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model {
    protected $fillable = ['filename', 'disk', 'type', 'size_bytes', 'status'];

    public function getFormattedSizeAttribute(): string {
        $bytes = $this->size_bytes;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 2) . ' KB';
    }
}
