<?php

namespace App\Repositories\Eloquent;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PageRepository implements PageRepositoryInterface {
    public function getPublishedBySlug(string $slug): ?Page {
        return Page::published()->where('slug', $slug)->firstOrFail();
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator {
        return Page::latest()->paginate($perPage);
    }

    public function create(array $data): Page {
        return Page::create($data);
    }

    public function update(Page $page, array $data): bool {
        return $page->update($data);
    }

    public function delete(Page $page): bool {
        return $page->delete();
    }
}
