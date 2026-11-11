<?php

namespace App\Repositories\Contracts;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PageRepositoryInterface {
    public function getPublishedBySlug(string $slug): ?Page;
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Page;
    public function update(Page $page, array $data): bool;
    public function delete(Page $page): bool;
}
