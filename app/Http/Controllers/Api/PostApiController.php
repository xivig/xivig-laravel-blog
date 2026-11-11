<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostApiController extends Controller {
    public function index(Request $request): AnonymousResourceCollection {
        $posts = Post::published()
            ->with(['category', 'author', 'tags'])
            ->latest('published_at')
            ->paginate($request->integer('per_page', 10));

        return PostResource::collection($posts);
    }

    public function show(string $slug): PostResource {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['category', 'author', 'tags'])
            ->firstOrFail();

        return new PostResource($post);
    }

    public function trending(): AnonymousResourceCollection {
        $posts = Post::published()
            ->with(['category', 'author'])
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        return PostResource::collection($posts);
    }
}
