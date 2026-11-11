<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Str;

class SeoHelper {
    public static function generateMetaTags(array $custom = []): array {
        $defaults = [
            'title'       => Setting::get('site_title', 'XIVIG Maison Gazette'),
            'description' => Setting::get('site_description', 'Chronicles of Haute Couture, Horology & Fine Savoir-Faire.'),
            'keywords'    => Setting::get('site_keywords', 'luxury, haute couture, horology, craftsmanship, atelier, editorial'),
            'canonical'   => url()->current(),
            'robots'      => 'index, follow, max-image-preview:large',
            'author'      => 'Maison XIVIG Editorial Desk',
            'language'    => 'en-US',
            'geo'         => 'FR-75;Paris',
            'rating'      => 'General',
            'copyright'   => '© ' . date('Y') . ' Maison XIVIG. All rights reserved.',
        ];

        return array_merge($defaults, array_filter($custom));
    }

    public static function readingTime(string $content): int {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int) ceil($wordCount / 200));
    }

    public static function extractTableOfContents(string $html): array {
        $toc = [];
        if (preg_match_all('/<h([2-3])([^>]*)>(.*?)<\/h\1>/i', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $index => $match) {
                $level = (int) $match[1];
                $title = strip_tags($match[3]);
                $slug = Str::slug($title) ?: 'section-' . ($index + 1);
                $toc[] = [
                    'level' => $level,
                    'title' => $title,
                    'slug'  => $slug,
                ];
            }
        }
        return $toc;
    }
}
