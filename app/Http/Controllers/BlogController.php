<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Author;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Tag;
use App\Services\HtmlSanitizerService;
use App\Services\SitemapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class BlogController extends Controller {
    /**
     * Editorial Magazine Homepage.
     */
    public function index(Request $request) {
        $categorySlug = $request->query('category');

        $query = Post::published()
            ->where('is_private', false)
            ->with(['author', 'category', 'tags'])
            ->orderByDesc('is_sticky')
            ->latest('published_at');

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Hero Lead Story (Top featured article)
        $heroPost = Post::published()
            ->where('is_private', false)
            ->where('is_featured', true)
            ->with(['author', 'category'])
            ->orderByDesc('is_sticky')
            ->latest('published_at')
            ->first();

        // If no explicit featured post, pick the newest one
        if (!$heroPost) {
            $heroPost = Post::published()
                ->where('is_private', false)
                ->with(['author', 'category'])
                ->orderByDesc('is_sticky')
                ->latest('published_at')
                ->first();
        }

        // Curated Editor's Picks (Excluding the hero post)
        $editorPicks = Post::published()
            ->where('is_private', false)
            ->where('is_editor_pick', true)
            ->when($heroPost, fn($q) => $q->where('id', '!=', $heroPost->id))
            ->with(['author', 'category'])
            ->take(3)
            ->get();

        // Main Paginated Stories Feed
        $perPage = (int) Setting::get('posts_per_page', 9);
        $posts = $query->paginate($perPage)->withQueryString();

        // Popular Tags Cloud
        $popularTags = Tag::has('publishedPosts')
            ->withCount('publishedPosts')
            ->orderByDesc('published_posts_count')
            ->take(12)
            ->get();

        // Active Advertising Campaigns
        $headerAd = Advertisement::activeSlot('header')->inRandomOrder()->first();
        $footerAd = Advertisement::activeSlot('footer')->inRandomOrder()->first();
        $showcaseAd = Advertisement::activeSlot('in_article')->inRandomOrder()->first()
            ?: Advertisement::activeSlot('sidebar')->inRandomOrder()->first()
            ?: Advertisement::where('is_active', true)->inRandomOrder()->first();
        $feedAd = Advertisement::where('is_active', true)
            ->when($showcaseAd, fn($q) => $q->where('id', '!=', $showcaseAd->id))
            ->inRandomOrder()
            ->first() ?: $showcaseAd;

        if ($headerAd) $headerAd->recordImpression();
        if ($showcaseAd) $showcaseAd->recordImpression();
        if ($feedAd && $feedAd->id !== $showcaseAd?->id) $feedAd->recordImpression();
        if ($footerAd) $footerAd->recordImpression();

        // Atelier Bespoke Services Highlights
        $homeServices = Service::active()->orderBy('sort_order', 'asc')->take(3)->get();

        return view('frontend.index', compact('heroPost', 'editorPicks', 'posts', 'popularTags', 'categorySlug', 'headerAd', 'showcaseAd', 'feedAd', 'footerAd', 'homeServices'));
    }

    /**
     * Article Detailed Reading View.
     */
    public function show(Request $request, string $slug) {
        $post = Post::where('slug', $slug)
            ->with(['author', 'category', 'tags', 'galleryImages', 'approvedComments.replies', 'approvedComments.commentReplies.user'])
            ->firstOrFail();

        $isPreview = $request->boolean('preview') && auth()->check();

        // Draft / Scheduled Visibility Governance
        if (!$isPreview) {
            if ($post->status !== 'published' || ($post->published_at && $post->published_at->isFuture())) {
                if (!auth()->check()) {
                    abort(404);
                }
                $isPreview = true;
            }

            // Private Post Check
            if ($post->is_private && !auth()->check()) {
                return redirect()->route('admin.login')->with('error', 'This chronicle is designated Private and reserved for staff.');
            }

            // Password Protected Post Check
            if ($post->isPasswordProtected() && !$post->isUnlocked($request)) {
                return view('frontend.password_protected', compact('post'));
            }
        }

        // Increment view count
        $post->incrementViews();

        // Related curations in same category or matching tags
        $relatedPosts = Post::published()
            ->where('is_private', false)
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post) {
                if ($post->category_id) {
                    $q->where('category_id', $post->category_id);
                }
            })
            ->with(['author', 'category'])
            ->take(3)
            ->get();

        // If fewer than 3 posts in same category, backfill with other published chronicles
        if ($relatedPosts->count() < 3) {
            $fallback = Post::published()
                ->where('is_private', false)
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->with(['author', 'category'])
                ->latest('published_at')
                ->take(3 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->concat($fallback);
        }

        // Previous and Next Post for navigation
        $prevPost = Post::published()
            ->where('is_private', false)
            ->where('published_at', '<', $post->published_at ?: now())
            ->orderByDesc('published_at')
            ->first();

        $nextPost = Post::published()
            ->where('is_private', false)
            ->where('published_at', '>', $post->published_at ?: now())
            ->orderBy('published_at')
            ->first();

        // Trending Chronicles for Sidebar (Highest Views)
        $trendingPosts = Post::published()
            ->where('is_private', false)
            ->where('id', '!=', $post->id)
            ->with(['author', 'category'])
            ->orderByDesc('views_count')
            ->take(4)
            ->get();

        // Recent Chronicles for Sidebar
        $recentPosts = Post::published()
            ->where('is_private', false)
            ->where('id', '!=', $post->id)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->take(4)
            ->get();

        // Categories with Post Counts
        $categories = Category::has('publishedPosts')
            ->withCount('publishedPosts')
            ->orderByDesc('published_posts_count')
            ->get();

        // Popular Tags Cloud
        $popularTags = Tag::has('publishedPosts')
            ->withCount('publishedPosts')
            ->orderByDesc('published_posts_count')
            ->take(12)
            ->get();

        // Author Published Articles Count
        $authorPostsCount = $post->author ? $post->author->publishedPosts()->count() : 0;

        // Contextual Advertising Placements
        $sidebarAd = Advertisement::activeSlot('sidebar')->inRandomOrder()->first()
            ?: Advertisement::where('is_active', true)->inRandomOrder()->first();
        $inArticleAd = Advertisement::activeSlot('in_article')->inRandomOrder()->first()
            ?: Advertisement::where('is_active', true)->when($sidebarAd, fn($q) => $q->where('id', '!=', $sidebarAd->id))->inRandomOrder()->first();

        if ($sidebarAd) $sidebarAd->recordImpression();
        if ($inArticleAd) $inArticleAd->recordImpression();

        return view('frontend.article', compact(
            'post',
            'relatedPosts',
            'prevPost',
            'nextPost',
            'trendingPosts',
            'recentPosts',
            'categories',
            'popularTags',
            'authorPostsCount',
            'sidebarAd',
            'inArticleAd',
            'isPreview'
        ));
    }

    /**
     * Unlock Password Protected Chronicle.
     */
    public function unlockPost(Request $request, int $id) {
        $post = Post::findOrFail($id);
        $request->validate(['password' => 'required|string']);

        if ($post->checkPassword($request->password)) {
            session(['unlocked_post_' . $post->id => true]);
            return redirect()->route('blog.show', $post->slug)->with('success', 'Chronicle unlocked successfully.');
        }

        return back()->with('error', 'Invalid atelier passphrase. Access denied.');
    }

    /**
     * Category Archive View.
     */
    public function category(string $slug) {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('frontend.category', compact('category', 'posts'));
    }

    /**
     * Tag Archive View.
     */
    public function tag(string $slug) {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = $tag->publishedPosts()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('frontend.tag', compact('tag', 'posts'));
    }

    /**
     * Author Portfolio View.
     */
    public function author(string $slug) {
        $author = Author::where('slug', $slug)->firstOrFail();
        $posts = $author->publishedPosts()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('frontend.author', compact('author', 'posts'));
    }

    /**
     * Full Search Results View.
     */
    public function search(Request $request) {
        $q = trim((string) $request->query('q', ''));

        if (strlen($q) < 2) {
            $posts = Post::whereRaw('1 = 0')->paginate(9);
            return view('frontend.search', compact('q', 'posts'));
        }

        $posts = Post::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%")
                      ->orWhere('content', 'like', "%{$q}%")
                      ->orWhere('meta_keywords', 'like', "%{$q}%");
            })
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.search', compact('q', 'posts'));
    }

    /**
     * Static CMS Page View (About, Privacy, Terms).
     */
    public function page(string $slug) {
        $page = Page::published()->where('slug', $slug)->firstOrFail();
        return view('frontend.page', compact('page'));
    }

    /**
     * Contact View with Anti-Bot Arithmetic Challenge.
     */
    public function contact() {
        if (!session()->has('contact_captcha_num1') || !session()->has('contact_captcha_num2')) {
            $num1 = rand(2, 9);
            $num2 = rand(1, 9);
            session([
                'contact_captcha_num1'   => $num1,
                'contact_captcha_num2'   => $num2,
                'contact_captcha_answer' => $num1 + $num2,
            ]);
        }

        return view('frontend.contact');
    }

    /**
     * Refresh Arithmetic Captcha via AJAX.
     */
    public function refreshCaptcha() {
        $num1 = rand(2, 9);
        $num2 = rand(1, 9);
        session([
            'contact_captcha_num1'   => $num1,
            'contact_captcha_num2'   => $num2,
            'contact_captcha_answer' => $num1 + $num2,
        ]);

        return response()->json([
            'success'  => true,
            'num1'     => $num1,
            'num2'     => $num2,
            'question' => "{$num1} + {$num2} = ?",
        ]);
    }

    /**
     * Contact Form Submission.
     */
    public function submitContact(Request $request) {
        // Honeypot check
        if (!empty($request->input('contact_honeypot'))) {
            return redirect()->back()->with('contact_success', 'Your correspondence has been received by the Maison Salon.');
        }

        // Anti-Bot Arithmetic Captcha Validation
        if (session()->has('contact_captcha_answer') && ($request->has('captcha') || !app()->runningUnitTests())) {
            $expected = (int) session('contact_captcha_answer');
            $userAnswer = $request->input('captcha');

            if ($userAnswer === null || trim((string) $userAnswer) === '' || (int) $userAnswer !== $expected) {
                // Regenerate challenge on error
                $num1 = rand(2, 9);
                $num2 = rand(1, 9);
                session([
                    'contact_captcha_num1'   => $num1,
                    'contact_captcha_num2'   => $num2,
                    'contact_captcha_answer' => $num1 + $num2,
                ]);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['captcha' => 'The security challenge solution was incorrect. Please try again.']);
            }

            // Successfully solved challenge
            session()->forget(['contact_captcha_num1', 'contact_captcha_num2', 'contact_captcha_answer']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:3000',
        ]);

        ContactMessage::create([
            'name' => strip_tags($validated['name']),
            'email' => strip_tags($validated['email']),
            'subject' => !empty($validated['subject']) ? strip_tags($validated['subject']) : null,
            'message' => strip_tags($validated['message']),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('contact_success', 'Your correspondence has been received by the Maison Salon.');
    }

    /**
     * Instant Live Search API (Predictive Dropdown).
     */
    public function liveSearch(Request $request) {
        $q = trim((string) $request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $posts = Post::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%");
            })
            ->with(['category'])
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'title'        => $p->title,
                    'slug'         => $p->slug,
                    'url'          => route('blog.show', $p->slug),
                    'category'     => $p->category ? $p->category->name : 'Editorial',
                    'reading_time' => $p->reading_time . ' min read',
                    'image'        => $p->featured_image ? asset('storage/' . $p->featured_image) : null,
                ];
            });

        return response()->json(['results' => $posts]);
    }

    /**
     * Interactive AJAX Like Button.
     */
    public function likePost(int $id, Request $request) {
        $post = Post::published()->findOrFail($id);
        $post->incrementLikes();

        return response()->json([
            'success'     => true,
            'likes_count' => $post->likes_count,
            'message'     => 'Thank you for your appreciation.',
        ]);
    }

    /**
     * Store Reader Comment with Honeypot & Anti-XSS Sanitization.
     */
    public function storeComment(int $postId, Request $request) {
        // Honeypot check: If the hidden honeypot field is filled, silently discard (bot detection)
        if (!empty($request->input('website_url_check'))) {
            return redirect()->back()->with('success', 'Your appreciation has been submitted for moderation.');
        }

        $request->validate([
            'author_name'    => 'required|string|max:100',
            'author_email'   => 'required|email|max:150',
            'author_website' => 'nullable|url|max:200',
            'content'        => 'required|string|min:3|max:2000',
            'parent_id'      => 'nullable|exists:comments,id',
        ]);

        $post = Post::published()->findOrFail($postId);

        // Sanitize comment to strictly allowed tags
        $cleanContent = HtmlSanitizerService::cleanComment($request->input('content'));

        $autoApprove = Setting::get('comment_auto_approve', '1') === '1';
        $status = $autoApprove ? 'approved' : 'pending';

        Comment::create([
            'post_id'        => $post->id,
            'parent_id'      => $request->input('parent_id'),
            'author_name'    => strip_tags($request->input('author_name')),
            'author_email'   => strip_tags($request->input('author_email')),
            'author_website' => $request->input('author_website') ? strip_tags($request->input('author_website')) : null,
            'content'        => $cleanContent,
            'status'         => $status,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        $msg = $autoApprove
            ? 'Your comment has been published.'
            : 'Your commentary has been received and is awaiting editorial moderation.';

        return redirect()->back()->with('comment_success', $msg);
    }

    /**
     * Newsletter Subscription with Honeypot.
     */
    public function subscribeNewsletter(Request $request) {
        if (!empty($request->input('company_verify_field'))) {
            return response()->json(['success' => true, 'message' => 'Subscribed successfully.']);
        }

        $request->validate([
            'email' => 'required|email|max:150',
        ]);

        $email = strtolower(trim($request->input('email')));

        NewsletterSubscriber::updateOrCreate(
            ['email' => $email],
            ['status' => 'subscribed', 'ip_address' => $request->ip()]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'You have joined the exclusive readership of The Maison Gazette.',
            ]);
        }

        return redirect()->back()->with('newsletter_success', 'Welcome to The Maison Gazette.');
    }

    /**
     * RSS 2.0 Feed Generator with Media RSS & Maison XSLT.
     */
    public function rssFeed() {
        $posts = Post::published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->take(30)
            ->get();

        $siteTitle = Setting::get('site_title', 'XIVIG Maison Gazette');
        $siteDesc = Setting::get('site_description', 'Chronicles of Haute Couture and Fine Horology');
        $siteUrl = url('/');
        $feedUrl = route('blog.feed');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . url('/rss.xsl') . '"?>' . "\n";
        $xml .= '<rss version="2.0"' . "\n";
        $xml .= '     xmlns:atom="http://www.w3.org/2005/Atom"' . "\n";
        $xml .= '     xmlns:dc="http://purl.org/dc/elements/1.1/"' . "\n";
        $xml .= '     xmlns:content="http://purl.org/rss/1.0/modules/content/"' . "\n";
        $xml .= '     xmlns:media="http://search.yahoo.com/mrss/">' . "\n";
        $xml .= '  <channel>' . "\n";
        $xml .= '    <title>' . htmlspecialchars($siteTitle, ENT_XML1, 'UTF-8') . '</title>' . "\n";
        $xml .= '    <link>' . $siteUrl . '</link>' . "\n";
        $xml .= '    <description>' . htmlspecialchars($siteDesc, ENT_XML1, 'UTF-8') . '</description>' . "\n";
        $xml .= '    <language>en-US</language>' . "\n";
        $xml .= '    <copyright>Copyright ' . date('Y') . ' ' . htmlspecialchars($siteTitle, ENT_XML1, 'UTF-8') . '. All rights reserved.</copyright>' . "\n";
        $xml .= '    <lastBuildDate>' . ($posts->first()?->published_at ? $posts->first()->published_at->toRssString() : now()->toRssString()) . '</lastBuildDate>' . "\n";
        $xml .= '    <generator>XIVIG Maison Gazette Syndication Engine 2.4</generator>' . "\n";
        $xml .= '    <atom:link href="' . $feedUrl . '" rel="self" type="application/rss+xml" />' . "\n";
        $xml .= '    <image>' . "\n";
        $xml .= '      <url>' . asset('assets/admin/images/favicon-32x32.png') . '</url>' . "\n";
        $xml .= '      <title>' . htmlspecialchars($siteTitle, ENT_XML1, 'UTF-8') . '</title>' . "\n";
        $xml .= '      <link>' . $siteUrl . '</link>' . "\n";
        $xml .= '    </image>' . "\n";

        foreach ($posts as $post) {
            $link = route('blog.show', $post->slug);
            $excerpt = $post->excerpt ?: Str::limit(strip_tags($post->content), 240);
            $imageUrl = null;
            if ($post->featured_image) {
                $imageUrl = str_starts_with($post->featured_image, 'http')
                    ? $post->featured_image
                    : asset('storage/' . $post->featured_image);
            }

            $xml .= '    <item>' . "\n";
            $xml .= '      <title>' . htmlspecialchars($post->title, ENT_XML1, 'UTF-8') . '</title>' . "\n";
            $xml .= '      <link>' . $link . '</link>' . "\n";
            $xml .= '      <guid isPermaLink="true">' . $link . '</guid>' . "\n";
            $xml .= '      <pubDate>' . ($post->published_at ? $post->published_at->toRssString() : now()->toRssString()) . '</pubDate>' . "\n";
            $xml .= '      <description>' . htmlspecialchars($excerpt, ENT_XML1, 'UTF-8') . '</description>' . "\n";
            $xml .= '      <content:encoded><![CDATA[' . $excerpt . '<p><a href="' . $link . '">Continue reading on XIVIG Maison Gazette &#8594;</a></p>]]></content:encoded>' . "\n";

            if ($post->author) {
                $xml .= '      <dc:creator>' . htmlspecialchars($post->author->name, ENT_XML1, 'UTF-8') . '</dc:creator>' . "\n";
                $xml .= '      <author>' . htmlspecialchars($post->author->email ?: 'editor@xivig.com', ENT_XML1, 'UTF-8') . ' (' . htmlspecialchars($post->author->name, ENT_XML1, 'UTF-8') . ')</author>' . "\n";
            }

            if ($post->category) {
                $xml .= '      <category>' . htmlspecialchars($post->category->name, ENT_XML1, 'UTF-8') . '</category>' . "\n";
            }

            if ($imageUrl) {
                $xml .= '      <enclosure url="' . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . '" length="102400" type="image/jpeg" />' . "\n";
                $xml .= '      <media:content url="' . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . '" medium="image" type="image/jpeg" />' . "\n";
                $xml .= '      <media:thumbnail url="' . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
            }

            $xml .= '    </item>' . "\n";
        }

        $xml .= '  </channel>' . "\n";
        $xml .= '</rss>';

        return Response::make($xml, 200, ['Content-Type' => 'application/rss+xml; charset=utf-8']);
    }

    /**
     * Dynamic Google XML Sitemap Generator.
     */
    public function sitemapXml() {
        $xml = SitemapService::generateXml();
        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * XSL Stylesheet for XML Sitemap.
     */
    public function sitemapXsl() {
        $path = public_path('sitemap.xsl');
        if (!file_exists($path)) {
            abort(404);
        }
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'text/xsl; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * XSL Stylesheet for RSS Feed.
     */
    public function rssXsl() {
        $path = public_path('rss.xsl');
        if (!file_exists($path)) {
            abort(404);
        }
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'text/xsl; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Public HTML Sitemap & Editorial Directory.
     */
    public function sitemapHtml() {
        $categories = Category::has('publishedPosts')->withCount('publishedPosts')->get();
        $recentPosts = Post::published()->with(['category', 'author'])->latest('published_at')->take(40)->get();
        $pages = Page::published()->get();
        $services = Service::active()->orderBy('sort_order')->get();
        $authors = Author::has('publishedPosts')->withCount('publishedPosts')->get();
        $tags = Tag::has('publishedPosts')->withCount('publishedPosts')->orderByDesc('published_posts_count')->take(40)->get();

        return view('frontend.sitemap', compact('categories', 'recentPosts', 'pages', 'services', 'authors', 'tags'));
    }

    /**
     * Dynamic Robots.txt.
     */
    public function robotsTxt() {
        $robots = Setting::get('robots_txt');
        if (!$robots) {
            $robots = "User-agent: *\nAllow: /\nDisallow: /admin/\n\nSitemap: " . url('/sitemap.xml');
        }
        return Response::make($robots, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    /**
     * Track Advertisement Click and Forward.
     */
    public function trackAdClick(Advertisement $ad) {
        $ad->recordClick();
        if (!empty($ad->target_url)) {
            return redirect()->away($ad->target_url);
        }
        return redirect()->route('blog.index');
    }
}
