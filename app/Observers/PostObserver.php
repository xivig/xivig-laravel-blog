<?php

namespace App\Observers;

use App\Helpers\SeoHelper;
use App\Models\Post;
use App\Models\Revision;
use App\Services\HtmlSanitizerService;
use Illuminate\Support\Facades\Auth;

class PostObserver {
    public function saving(Post $post): void {
        if (!empty($post->content)) {
            $post->content = HtmlSanitizerService::cleanArticle($post->content);
        }
    }

    public function creating(Post $post): void {
        if (!empty($post->content) && empty($post->reading_time)) {
            $post->reading_time = SeoHelper::readingTime($post->content);
        }
    }

    public function updating(Post $post): void {
        if ($post->isDirty('content')) {
            $post->reading_time = SeoHelper::readingTime($post->content);

            // Create revision archive
            try {
                Revision::create([
                    'user_id'           => Auth::id(),
                    'revisionable_type' => Post::class,
                    'revisionable_id'   => $post->id,
                    'title'             => $post->getOriginal('title'),
                    'content'           => $post->getOriginal('content'),
                    'summary'           => 'Automated editorial revision snapshot',
                ]);
            } catch (\Throwable $e) {}
        }
    }

    public function saved(Post $post): void {
        \App\Services\SitemapService::clearCache();
    }

    public function deleted(Post $post): void {
        \App\Services\SitemapService::clearCache();
    }
}
