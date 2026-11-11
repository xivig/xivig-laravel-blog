<?php

namespace App\Observers;

use App\Models\Page;
use App\Models\Revision;
use App\Services\HtmlSanitizerService;
use Illuminate\Support\Facades\Auth;

class PageObserver {
    public function saving(Page $page): void {
        if (!empty($page->content)) {
            $page->content = HtmlSanitizerService::cleanPage($page->content);
        }
    }

    public function updating(Page $page): void {
        if ($page->isDirty('content')) {
            try {
                Revision::create([
                    'user_id'           => Auth::id(),
                    'revisionable_type' => Page::class,
                    'revisionable_id'   => $page->id,
                    'title'             => $page->getOriginal('title'),
                    'content'           => $page->getOriginal('content'),
                    'summary'           => 'Static page content revision snapshot',
                ]);
            } catch (\Throwable $e) {}
        }
    }

    public function saved(Page $page): void {
        \App\Services\SitemapService::clearCache();
    }

    public function deleted(Page $page): void {
        \App\Services\SitemapService::clearCache();
    }
}
