<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts() {
        return $this->belongsToMany(Post::class);
    }

    public function publishedPosts() {
        return $this->belongsToMany(Post::class)->where('status', 'published');
    }
}
