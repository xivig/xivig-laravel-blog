<?php

namespace App\Enums;

enum WidgetType: string {
    case RECENT_POSTS = 'recent_posts';
    case TRENDING = 'trending';
    case POPULAR = 'popular';
    case ADS = 'ads';
    case NEWSLETTER = 'newsletter';
    case CATEGORIES = 'categories';
    case ARCHIVE = 'archive';
    case TAGS = 'tags';
    case CUSTOM_HTML = 'custom_html';

    public function label(): string {
        return match ($this) {
            self::RECENT_POSTS => 'Recent Chronicles',
            self::TRENDING => 'Trending In Atelier',
            self::POPULAR => 'Most Read Chronicles',
            self::ADS => 'Maison Showcase Advertisement',
            self::NEWSLETTER => 'VIP Atelier Dispatch Box',
            self::CATEGORIES => 'Curated Categories Index',
            self::ARCHIVE => 'Chronological Archive',
            self::TAGS => 'Aesthetic Tag Cloud',
            self::CUSTOM_HTML => 'Bespoke Custom HTML',
        };
    }
}
