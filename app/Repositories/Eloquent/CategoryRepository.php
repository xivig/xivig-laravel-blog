<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface {
    public function getAllWithCounts(): Collection {
        return Category::withCount(['publishedPosts'])->orderBy('name')->get();
    }

    public function findBySlug(string $slug): ?Category {
        return Category::where('slug', $slug)->firstOrFail();
    }
}
