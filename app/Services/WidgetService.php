<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Widget;
use Illuminate\Database\Eloquent\Collection;

class WidgetService {
    public static function getWidgetsForArea(string $area): Collection {
        return Widget::active()->area($area)->get();
    }

    public static function renderWidgetData(Widget $widget): array {
        return match ($widget->type) {
            'recent_posts' => ['posts' => Post::published()->latest('published_at')->take(4)->get()],
            'trending'     => ['posts' => Post::published()->orderByDesc('views_count')->take(4)->get()],
            'popular'      => ['posts' => Post::published()->orderByDesc('likes_count')->take(4)->get()],
            'categories'   => ['categories' => Category::withCount('publishedPosts')->get()],
            'tags'         => ['tags' => Tag::withCount('publishedPosts')->get()],
            default        => [],
        };
    }
}
