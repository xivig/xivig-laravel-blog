<?php

namespace App\Providers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Observers\PageObserver;
use App\Observers\PostObserver;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PageRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\PageRepository;
use App\Repositories\Eloquent\PostRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    public function boot(): void {
        // Enforce 191-character string length for MySQL utf8mb4 compatibility
        Schema::defaultStringLength(191);

        // Register Model Observers
        Post::observe(PostObserver::class);
        Page::observe(PageObserver::class);

        // Use Bootstrap 5 Pagination
        Paginator::useBootstrapFive();

        // Share common blog data with all frontend views
        View::composer('frontend.*', function ($view) {
            try {
                $categories = Category::withCount(['publishedPosts' => function ($q) {
                    $q->where('status', 'published');
                }])->orderBy('name')->get();

                $siteSettings = [
                    'title'       => Setting::get('site_title', 'XIVIG Maison Gazette'),
                    'tagline'     => Setting::get('site_tagline', 'The Haute Couture & Horology Journal'),
                    'description' => Setting::get('site_description', 'Chronicles of bespoke craftsmanship, luxury timepieces, and haute couture.'),
                    'logo'                => Setting::get('site_logo'),
                    'email'               => Setting::get('contact_email', 'gazette@xivig.com'),
                    'google_verification' => Setting::get('google_verification', Setting::get('google_search_console_key')),
                ];

                $headerMenu = Menu::with(['rootItems.children'])->where('location', 'header')->where('is_active', true)->first();
                $footerMenu = Menu::with(['rootItems.children'])->where('location', 'footer')->where('is_active', true)->first();

                // Retrieve active advertisement placements
                $activeAds = Advertisement::where('is_active', true)->get();
                $headerAd = $activeAds->where('slot', 'header')->first();
                $footerAd = $activeAds->where('slot', 'footer')->first();

                // Retrieve active social links
                $socialLinks = SocialLink::where('is_active', true)->orderBy('order')->get()->keyBy('platform');

                $view->with([
                    'globalCategories' => $categories,
                    'globalSettings'   => $siteSettings,
                    'headerMenu'       => $headerMenu,
                    'footerMenu'       => $footerMenu,
                    'globalActiveAds'  => $activeAds,
                    'globalHeaderAd'   => $headerAd,
                    'globalFooterAd'   => $footerAd,
                    'globalSocials'    => $socialLinks,
                ]);
            } catch (\Exception $e) {
                $view->with([
                    'globalCategories' => collect([]),
                    'headerMenu'       => null,
                    'footerMenu'       => null,
                    'globalActiveAds'  => collect([]),
                    'globalHeaderAd'   => null,
                    'globalFooterAd'   => null,
                    'globalSocials'    => collect([]),
                    'globalSettings'   => [
                        'title'       => 'XIVIG Maison Gazette',
                        'tagline'     => 'The Haute Couture & Horology Journal',
                        'description' => 'Chronicles of bespoke craftsmanship, luxury timepieces, and haute couture.',
                        'logo'        => null,
                        'email'       => 'gazette@xivig.com',
                    ],
                ]);
            }
        });

        // Share common notifications & metrics with all admin views
        View::composer('admin.*', function ($view) {
            try {
                $pendingCount = Comment::where('status', 'pending')->count();
                $recentPending = Comment::with('post')
                    ->where('status', 'pending')
                    ->latest()
                    ->take(5)
                    ->get();

                $unreadChat = 0;
                $unreadEmails = 0;
                $unreadInquiries = 0;

                if (\Illuminate\Support\Facades\Schema::hasTable('contact_messages')) {
                    $unreadInquiries = \App\Models\ContactMessage::where('is_read', false)->count();
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('chat_messages')) {
                    $userId = auth()->id();
                    $unreadChat = \App\Models\ChatMessage::where('is_read', false)
                        ->where(function ($q) use ($userId) {
                            $q->where('recipient_id', $userId)
                              ->orWhere(function ($sq) use ($userId) {
                                  $sq->whereNull('recipient_id')
                                     ->where('sender_id', '!=', $userId);
                              });
                        })->count();
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('admin_emails')) {
                    $unreadEmails = \App\Models\AdminEmail::where('folder', 'inbox')
                        ->where('is_read', false)
                        ->count();
                }

                $view->with([
                    'unreadNotificationsCount' => $pendingCount,
                    'headerNotifications'      => $recentPending,
                    'unreadInquiriesCount'     => $unreadInquiries,
                    'unreadChatCount'          => $unreadChat,
                    'unreadContactCount'       => $unreadInquiries,
                    'unreadEmailsCount'        => $unreadEmails,
                    'totalDeskAlerts'          => $unreadInquiries + $unreadEmails + $unreadChat + $pendingCount,
                    'systemTimezone'           => Setting::get('timezone', config('app.timezone', 'UTC')),
                ]);
            } catch (\Exception $e) {
                $view->with([
                    'unreadNotificationsCount' => 0,
                    'headerNotifications'      => collect([]),
                    'unreadInquiriesCount'     => 0,
                    'unreadChatCount'          => 0,
                    'unreadContactCount'       => 0,
                    'unreadEmailsCount'        => 0,
                    'totalDeskAlerts'          => 0,
                    'systemTimezone'           => 'UTC',
                ]);
            }
        });

        // Configure Rate Limiters
        RateLimiter::for('comments', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('likes', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('admin-login', function (Request $request) {
            $email = (string) $request->input('email');
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perMinute(5)->by(strtolower($email) . '|' . $request->ip()),
            ];
        });

        RateLimiter::for('admin-two-factor', function (Request $request) {
            $twoFactorId = (string) $request->session()->get('auth.2fa.id');
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perMinute(5)->by($twoFactorId . '|' . $request->ip()),
            ];
        });

        RateLimiter::for('admin-two-factor-resend', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}
