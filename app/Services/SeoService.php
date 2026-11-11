<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Str;

class SeoService {
    /**
     * Compute a comprehensive, modern SEO & content health audit (0-100 pts) for a given post.
     * Evaluates technical snippet factors, editorial substance, heading hierarchy,
     * accessible media alt text, linking architecture, and URL structure.
     * (Zero points awarded for meta keywords per modern Google search ranking standards).
     */
    public static function auditPost(Post $post): array {
        $score = 0;
        $checklist = [];

        // 1. Title Assessment (Max 15 pts)
        $title = $post->meta_title ?: $post->title;
        $titleLen = mb_strlen($title ?? '');
        if ($titleLen >= 40 && $titleLen <= 60) {
            $score += 15;
            $checklist[] = [
                'rule'    => 'SEO Title Snippet',
                'passed'  => true,
                'message' => "Optimal SERP title length ({$titleLen} characters, target 40–60).",
            ];
        } elseif ($titleLen >= 25 && $titleLen <= 75) {
            $score += 8;
            $checklist[] = [
                'rule'    => 'SEO Title Snippet',
                'passed'  => false,
                'message' => "Title length is {$titleLen} chars (recommended 40–60 to prevent snippet truncation).",
            ];
        } else {
            $checklist[] = [
                'rule'    => 'SEO Title Snippet',
                'passed'  => false,
                'message' => $titleLen === 0 ? 'Missing SEO title.' : "Title is {$titleLen} chars (too long or too short).",
            ];
        }

        // 2. Meta Description Assessment (Max 15 pts)
        $desc = $post->meta_description ?: $post->excerpt;
        $descLen = mb_strlen($desc ?? '');
        if ($descLen >= 120 && $descLen <= 160) {
            $score += 15;
            $checklist[] = [
                'rule'    => 'Meta Description Snippet',
                'passed'  => true,
                'message' => "Optimal description length ({$descLen} characters, target 120–160).",
            ];
        } elseif ($descLen >= 80 && $descLen <= 200) {
            $score += 8;
            $checklist[] = [
                'rule'    => 'Meta Description Snippet',
                'passed'  => false,
                'message' => "Description is {$descLen} chars (target 120–160 for optimal snippet display).",
            ];
        } else {
            $checklist[] = [
                'rule'    => 'Meta Description Snippet',
                'passed'  => false,
                'message' => $descLen === 0 ? 'Missing meta description and excerpt.' : "Description length ({$descLen} chars) deviates significantly from optimal SERP bounds.",
            ];
        }

        // 3. Editorial Substance & Content Depth (Max 25 pts)
        $cleanContent = strip_tags($post->content ?? '');
        $wordCount = str_word_count($cleanContent);
        if ($wordCount >= 750) {
            $score += 25;
            $checklist[] = [
                'rule'    => 'Content Depth & Substance',
                'passed'  => true,
                'message' => "Comprehensive long-form chronicle ({$wordCount} words). Substantial depth favors organic ranking.",
            ];
        } elseif ($wordCount >= 450) {
            $score += 18;
            $checklist[] = [
                'rule'    => 'Content Depth & Substance',
                'passed'  => true,
                'message' => "Solid editorial depth ({$wordCount} words, recommended 750+ for pillar chronicles).",
            ];
        } elseif ($wordCount >= 250) {
            $score += 10;
            $checklist[] = [
                'rule'    => 'Content Depth & Substance',
                'passed'  => false,
                'message' => "Moderate depth ({$wordCount} words). Expand toward 450+ words to avoid thin-content flags.",
            ];
        } else {
            $checklist[] = [
                'rule'    => 'Content Depth & Substance',
                'passed'  => false,
                'message' => "Thin content detected ({$wordCount} words). Articles under 250 words face indexation resistance.",
            ];
        }

        // 4. Semantic Heading Hierarchy (Max 15 pts)
        $hasH2 = preg_match('/<h2[^>]*>/i', $post->content ?? '');
        $hasH3 = preg_match('/<h3[^>]*>/i', $post->content ?? '');
        if ($hasH2 && $hasH3) {
            $score += 15;
            $checklist[] = [
                'rule'    => 'Headings Hierarchy (H2 & H3)',
                'passed'  => true,
                'message' => 'Exemplary heading structure: both H2 section headings and H3 subheadings present.',
            ];
        } elseif ($hasH2) {
            $score += 10;
            $checklist[] = [
                'rule'    => 'Headings Hierarchy (H2 & H3)',
                'passed'  => true,
                'message' => 'H2 section headings present. Add H3 subheadings to structure subsections.',
            ];
        } else {
            $checklist[] = [
                'rule'    => 'Headings Hierarchy (H2 & H3)',
                'passed'  => false,
                'message' => 'No H2 headings detected. Subheadings are required for editorial scannability and topic modeling.',
            ];
        }

        // 5. Image Optimization & Accessible Alt Text (Max 15 pts)
        $imgScore = 0;
        if (!empty($post->featured_image)) {
            $imgScore += 5;
            if (!empty($post->featured_image_alt)) {
                $imgScore += 5;
            } elseif (!empty($post->featured_image_caption)) {
                $imgScore += 3;
            }
        }

        // Check inline content images for alt text
        preg_match_all('/<img[^>]+>/i', $post->content ?? '', $imgMatches);
        $inlineImgs = $imgMatches[0] ?? [];
        if (count($inlineImgs) > 0) {
            $missingAlt = 0;
            foreach ($inlineImgs as $imgTag) {
                if (!preg_match('/alt=[\'"][^\'"]+[\'"]/i', $imgTag)) {
                    $missingAlt++;
                }
            }
            if ($missingAlt === 0) {
                $imgScore += 5;
            }
        } else {
            $imgScore += 5; // No inline images to penalize
        }

        $score += $imgScore;
        $checklist[] = [
            'rule'    => 'Media & Accessible Alt Text',
            'passed'  => $imgScore >= 10,
            'message' => !empty($post->featured_image)
                ? (!empty($post->featured_image_alt) ? 'Featured image present with dedicated accessible alt description.' : 'Featured image present; add dedicated alt text to maximize image search indexing.')
                : 'No featured photograph attached. Visual assets drastically elevate click-through rate.',
        ];

        // 6. Linking Architecture (Max 10 pts)
        preg_match_all('/<a\s+[^>]*href=[\'"]([^\'"]+)[\'"]/i', $post->content ?? '', $linkMatches);
        $linkCount = count($linkMatches[1] ?? []);
        if ($linkCount >= 2) {
            $score += 10;
            $checklist[] = [
                'rule'    => 'Internal & Contextual Linking',
                'passed'  => true,
                'message' => "Strong contextual linking detected ({$linkCount} links). Facilitates crawl depth and user journeys.",
            ];
        } elseif ($linkCount === 1) {
            $score += 5;
            $checklist[] = [
                'rule'    => 'Internal & Contextual Linking',
                'passed'  => false,
                'message' => "Only 1 link detected. Integrating 2+ internal chronicle references boosts authority.",
            ];
        } else {
            $checklist[] = [
                'rule'    => 'Internal & Contextual Linking',
                'passed'  => false,
                'message' => 'No links detected. Connect this chronicle to related topics or atelier services.',
            ];
        }

        // 7. URL Slug Cleanliness (Max 5 pts)
        $slug = $post->slug ?? '';
        $slugLen = strlen($slug);
        if ($slugLen > 0 && $slugLen <= 65 && preg_match('/^[a-z0-9\-]+$/', $slug)) {
            $score += 5;
            $checklist[] = [
                'rule'    => 'URL Slug Hygiene',
                'passed'  => true,
                'message' => "Clean, human-readable permalink structure (/{$slug}).",
            ];
        } else {
            $checklist[] = [
                'rule'    => 'URL Slug Hygiene',
                'passed'  => false,
                'message' => "Permalink contains non-standard characters or exceeds recommended length ({$slugLen} chars).",
            ];
        }

        // 8. Meta Keywords Information (0 pts - Modern Search Engine Standards)
        $checklist[] = [
            'rule'    => 'Meta Keywords Tag (Informational)',
            'passed'  => true,
            'message' => !empty($post->meta_keywords)
                ? 'Meta keywords documented for internal taxonomy. Note: Major search engines (Google, Bing) ignore this tag for ranking calculations.'
                : 'No meta keywords defined (optional; modern search engines do not utilize this tag for rankings).',
        ];

        // Grade and visual theme
        $grade = match(true) {
            $score >= 90 => 'A+',
            $score >= 80 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            default      => 'Needs Improvement',
        };

        $color = match(true) {
            $score >= 80 => 'success',
            $score >= 60 => 'warning',
            default      => 'danger',
        };

        return [
            'score'      => min(100, $score),
            'grade'      => $grade,
            'color'      => $color,
            'checklist'  => $checklist,
            'word_count' => $wordCount,
        ];
    }

    /**
     * Build Schema.org BlogPosting JSON-LD safely formatted.
     */
    public static function buildArticleSchema(Post $post): array {
        $siteTitle = Setting::get('site_title', 'Maison XIVIG');
        $siteLogo = asset('assets/images/branding/logo-CDpcaf-E.png');

        return [
            '@context'         => 'https://schema.org',
            '@type'            => 'BlogPosting',
            'headline'         => $post->title,
            'description'      => $post->excerpt,
            'image'            => $post->featured_image ? [asset('storage/' . $post->featured_image)] : [$siteLogo],
            'datePublished'    => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
            'dateModified'     => $post->updated_at->toIso8601String(),
            'author'           => [
                '@type' => 'Person',
                'name'  => $post->author ? $post->author->name : 'Maison XIVIG Editorial',
                'url'   => $post->author ? route('blog.author', $post->author->slug) : url('/'),
            ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => $siteTitle,
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => $siteLogo,
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => route('blog.show', $post->slug),
            ],
            'wordCount'        => str_word_count(strip_tags($post->content ?? '')),
            'articleSection'   => $post->category ? $post->category->name : 'Chronicles',
        ];
    }

    /**
     * Build Schema.org BreadcrumbList JSON-LD.
     */
    public static function buildBreadcrumbSchema(array $items): array {
        $elements = [];
        foreach ($items as $idx => $item) {
            $elements[] = [
                '@type'    => 'ListItem',
                'position' => $idx + 1,
                'name'     => $item['name'],
                'item'     => $item['url'],
            ];
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * Build Schema.org WebSite JSON-LD with SearchAction.
     */
    public static function buildWebSiteSchema(): array {
        $siteTitle = Setting::get('site_title', 'Maison XIVIG');
        $siteDesc = Setting::get('site_description', 'Haute Couture, Horology & Modern Luxury');

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => $siteTitle,
            'description'     => $siteDesc,
            'url'             => url('/'),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => url('/search?q={search_term_string}'),
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Build Schema.org Organization JSON-LD.
     */
    public static function buildOrganizationSchema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => Setting::get('site_title', 'Maison XIVIG'),
            'url'      => url('/'),
            'logo'     => asset('assets/images/branding/logo-CDpcaf-E.png'),
            'sameAs'   => [
                'https://instagram.com',
                'https://twitter.com',
                'https://linkedin.com',
            ],
        ];
    }

    /**
     * Provide automated internal linking recommendations for a given post.
     * Identifies published contextual candidates by shared category or tags.
     */
    public static function suggestInternalLinks(Post $post, int $limit = 5): array {
        $suggestions = [];

        // 1. Same category candidates
        $categoryCandidates = Post::publicVisible()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->take($limit)
            ->get();

        foreach ($categoryCandidates as $candidate) {
            $suggestions[$candidate->id] = [
                'id'          => $candidate->id,
                'title'       => $candidate->title,
                'url'         => route('blog.show', $candidate->slug),
                'reason'      => 'Shares category: ' . ($candidate->category ? $candidate->category->name : 'General'),
                'anchor_html' => '<a href="' . route('blog.show', $candidate->slug) . '">' . e($candidate->title) . '</a>',
            ];
        }

        // 2. Shared tags candidates
        if ($post->relationLoaded('tags') || $post->tags()->exists()) {
            $tagIds = $post->tags->pluck('id');
            if ($tagIds->isNotEmpty()) {
                $tagCandidates = Post::publicVisible()
                    ->where('id', '!=', $post->id)
                    ->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagIds))
                    ->latest('published_at')
                    ->take($limit)
                    ->get();

                foreach ($tagCandidates as $candidate) {
                    if (!isset($suggestions[$candidate->id])) {
                        $suggestions[$candidate->id] = [
                            'id'          => $candidate->id,
                            'title'       => $candidate->title,
                            'url'         => route('blog.show', $candidate->slug),
                            'reason'      => 'Shares topical tags with this chronicle',
                            'anchor_html' => '<a href="' . route('blog.show', $candidate->slug) . '">' . e($candidate->title) . '</a>',
                        ];
                    }
                }
            }
        }

        return array_slice(array_values($suggestions), 0, $limit);
    }

    /**
     * Dispatch published URLs to IndexNow / Search Engine instant indexing API.
     */
    public static function submitToIndexNow(array|string $urls): bool {
        $apiKey = Setting::get('indexnow_api_key', config('services.indexnow.key'));
        if (!$apiKey) {
            return false;
        }

        $urlList = (array) $urls;
        $host = parse_url(url('/'), PHP_URL_HOST);

        try {
            $payload = [
                'host'        => $host,
                'key'         => $apiKey,
                'keyLocation' => url("/{$apiKey}.txt"),
                'urlList'     => $urlList,
            ];

            \Illuminate\Support\Facades\Http::timeout(5)->post('https://api.indexnow.org/indexnow', $payload);
            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("IndexNow ping failed: " . $e->getMessage());
            return false;
        }
    }
}
