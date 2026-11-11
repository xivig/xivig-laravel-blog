<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\Tag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapService {
    /**
     * Cache duration for XML sitemap: 1 hour (3600 seconds)
     */
    protected const CACHE_KEY = 'atelier_sitemap_xml';
    protected const CACHE_TTL = 3600;

    /**
     * Generate standard XML sitemap for search engines with caching and Maison XSLT styling.
     */
    public static function generateXml(): string {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::buildXml();
        });
    }

    /**
     * Invalidate cached sitemap.
     */
    public static function clearCache(): void {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Build the raw XML payload.
     */
    public static function buildXml(): string {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . url('/sitemap.xsl') . '"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // 1. Homepage
        $xml .= self::formatUrl(url('/'), now(), 'daily', '1.0');

        // 2. Published Chronicles (Strictly public, non-private, non-scheduled)
        $posts = Post::publicVisible()->latest('published_at')->take(5000)->get();
        foreach ($posts as $post) {
            $lastMod = $post->updated_at ?? $post->published_at ?? now();
            $imageUrl = null;
            if ($post->featured_image) {
                $imageUrl = str_starts_with($post->featured_image, 'http')
                    ? $post->featured_image
                    : asset('storage/' . $post->featured_image);
            }

            $xml .= self::formatUrl(
                route('blog.show', $post->slug),
                $lastMod,
                'weekly',
                $post->is_featured ? '0.9' : '0.8',
                $imageUrl,
                $post->featured_image_alt ?: $post->title
            );
        }

        // 3. Atelier Services Hub & Offerings
        $xml .= self::formatUrl(route('services.index'), now(), 'weekly', '0.85');

        $services = Service::active()->orderBy('sort_order')->get();
        foreach ($services as $service) {
            $coverUrl = null;
            if ($service->cover_image) {
                $coverUrl = str_starts_with($service->cover_image, 'http')
                    ? $service->cover_image
                    : asset('storage/' . $service->cover_image);
            }

            $xml .= self::formatUrl(
                route('services.show', $service->slug),
                $service->updated_at ?? now(),
                'monthly',
                $service->is_featured ? '0.8' : '0.7',
                $coverUrl,
                $service->title
            );
        }

        // 4. Institutional & Editorial Pages
        $pages = Page::published()->get();
        foreach ($pages as $page) {
            $xml .= self::formatUrl(
                route('page.show', $page->slug),
                $page->updated_at ?? now(),
                'monthly',
                '0.65'
            );
        }

        // 5. Contact Desk & Syndication Hubs
        $xml .= self::formatUrl(route('contact'), now(), 'monthly', '0.7');
        $xml .= self::formatUrl(url('/sitemap'), now(), 'weekly', '0.6');
        $xml .= self::formatUrl(route('blog.feed'), now(), 'daily', '0.6');

        // 6. Editorial Categories
        $categories = Category::has('publishedPosts')->get();
        foreach ($categories as $cat) {
            $xml .= self::formatUrl(
                route('blog.category', $cat->slug),
                $cat->updated_at ?? now(),
                'weekly',
                '0.75'
            );
        }

        // 7. Authors & Curators
        $authors = Author::has('publishedPosts')->get();
        foreach ($authors as $author) {
            $xml .= self::formatUrl(
                route('blog.author', $author->slug),
                $author->updated_at ?? now(),
                'monthly',
                '0.7'
            );
        }

        // 8. Popular Tags (Tags with published posts)
        $tags = Tag::has('publishedPosts')->take(50)->get();
        foreach ($tags as $tag) {
            $xml .= self::formatUrl(
                route('blog.tag', $tag->slug),
                $tag->updated_at ?? now(),
                'weekly',
                '0.5'
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private static function formatUrl(string $loc, Carbon $lastMod, string $changefreq, string $priority, ?string $image = null, ?string $imageCaption = null): string {
        $entry = "    <url>\n";
        $entry .= "        <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n";
        $entry .= "        <lastmod>" . $lastMod->toIso8601String() . "</lastmod>\n";
        $entry .= "        <changefreq>{$changefreq}</changefreq>\n";
        $entry .= "        <priority>{$priority}</priority>\n";

        if ($image) {
            $entry .= "        <image:image>\n";
            $entry .= "            <image:loc>" . htmlspecialchars($image, ENT_XML1, 'UTF-8') . "</image:loc>\n";
            if ($imageCaption) {
                $entry .= "            <image:title>" . htmlspecialchars($imageCaption, ENT_XML1, 'UTF-8') . "</image:title>\n";
            }
            $entry .= "        </image:image>\n";
        }

        $entry .= "    </url>\n";
        return $entry;
    }
}
