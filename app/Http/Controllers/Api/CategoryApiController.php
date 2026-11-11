<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryApiController extends Controller {
    public function index(): AnonymousResourceCollection {
        $categories = Category::withCount('publishedPosts')->orderBy('name')->get();
        return CategoryResource::collection($categories);
    }

    public function show(string $slug): CategoryResource {
        $category = Category::where('slug', $slug)->withCount('publishedPosts')->firstOrFail();
        return new CategoryResource($category);
    }

    public function posts(Request $request, string $slug): AnonymousResourceCollection {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->publishedPosts()
            ->with(['author', 'tags'])
            ->latest('published_at')
            ->paginate($request->integer('per_page', 10));

        return PostResource::collection($posts);
    }
}
