<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface {
    public function getPublishedPaginated(int $perPage = 9): LengthAwarePaginator;
    public function getEditorPicks(int $limit = 3): Collection;
    public function getFeatured(int $limit = 1): ?Post;
    public function getTrending(int $limit = 4): Collection;
    public function findBySlug(string $slug): ?Post;
    public function getRelated(Post $post, int $limit = 3): Collection;
    public function create(array $data): Post;
    public function update(Post $post, array $data): bool;
    public function delete(Post $post): bool;
}
