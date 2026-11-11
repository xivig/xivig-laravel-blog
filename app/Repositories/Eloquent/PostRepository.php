<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface {
    public function getPublishedPaginated(int $perPage = 9): LengthAwarePaginator {
        return Post::published()
            ->with(['category', 'author', 'tags'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getEditorPicks(int $limit = 3): Collection {
        return Post::published()
            ->where('is_editor_pick', true)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    public function getFeatured(int $limit = 1): ?Post {
        return Post::published()
            ->where('is_featured', true)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->first();
    }

    public function getTrending(int $limit = 4): Collection {
        return Post::published()
            ->with('category')
            ->orderByDesc('views_count')
            ->take($limit)
            ->get();
    }

    public function findBySlug(string $slug): ?Post {
        return Post::published()
            ->where('slug', $slug)
            ->with(['category', 'author', 'tags', 'approvedComments.replies'])
            ->firstOrFail();
    }

    public function getRelated(Post $post, int $limit = 3): Collection {
        return Post::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    public function create(array $data): Post {
        return Post::create($data);
    }

    public function update(Post $post, array $data): bool {
        return $post->update($data);
    }

    public function delete(Post $post): bool {
        return $post->delete();
    }
}
