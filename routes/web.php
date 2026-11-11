<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ContactInquiryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\FileManagerController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ImageCropperController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NotFoundLogController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SEOController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Frontend\ServiceController as PublicServiceController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TaskManagerController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Editorial Routes (Maison XIVIG Gazette)
|--------------------------------------------------------------------------
*/
Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::redirect('/home', '/')->name('home');
Route::get('/article/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/author/{slug}', [BlogController::class, 'author'])->name('blog.author');
Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/ad/click/{ad}', [BlogController::class, 'trackAdClick'])->name('ad.click');

// Static CMS Pages & Contact Salon
Route::get('/contact', [BlogController::class, 'contact'])->name('contact');
Route::get('/contact/captcha-refresh', [BlogController::class, 'refreshCaptcha'])->name('contact.captcha.refresh');
Route::post('/contact', [BlogController::class, 'submitContact'])->middleware('throttle:comments')->name('contact.submit');
Route::get('/page/{slug}', [BlogController::class, 'page'])->name('page.show');

// Friendly Canonical Redirects for Legal & Editorial Codices
Route::redirect('/privacy', '/page/privacy-policy')->name('privacy');
Route::redirect('/privacy-policy', '/page/privacy-policy');
Route::redirect('/terms', '/page/terms-of-dispatch')->name('terms');
Route::redirect('/terms-of-dispatch', '/page/terms-of-dispatch');
Route::redirect('/ethics', '/page/editorial-ethics')->name('ethics');
Route::redirect('/editorial-ethics', '/page/editorial-ethics');
Route::redirect('/about', '/page/about-us')->name('about');
Route::redirect('/about-us', '/page/about-us');

// Interaction Endpoints (Rate Limited)
Route::post('/article/{id}/unlock', [BlogController::class, 'unlockPost'])
    ->middleware('throttle:10,1')
    ->name('blog.unlock');

Route::post('/article/{id}/like', [BlogController::class, 'likePost'])
    ->middleware('throttle:likes')
    ->name('blog.like');

Route::post('/article/{id}/comments', [BlogController::class, 'storeComment'])
    ->middleware('throttle:comments')
    ->name('blog.comments.store');

Route::post('/newsletter/subscribe', [BlogController::class, 'subscribeNewsletter'])
    ->name('newsletter.subscribe');

// Services & Bespoke Offerings
Route::get('/services', [PublicServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [PublicServiceController::class, 'show'])->name('services.show');
Route::post('/services/inquire', [PublicServiceController::class, 'inquire'])->name('services.inquire');

// Syndication & Automated SEO Files
Route::get('/robots.txt', [BlogController::class, 'robotsTxt'])->name('robots.txt');
Route::get('/feed.xml', [BlogController::class, 'rssFeed'])->name('blog.feed');
Route::get('/feed', [BlogController::class, 'rssFeed']);
Route::get('/rss.xml', [BlogController::class, 'rssFeed']);
Route::get('/rss', [BlogController::class, 'rssFeed']);
Route::get('/rss.xsl', [BlogController::class, 'rssXsl'])->name('blog.rss.xsl');
Route::get('/sitemap.xml', [BlogController::class, 'sitemapXml'])->name('blog.sitemap');
Route::get('/sitemap.xsl', [BlogController::class, 'sitemapXsl'])->name('blog.sitemap.xsl');
Route::get('/sitemap', [BlogController::class, 'sitemapHtml'])->name('blog.sitemap.html');

/*
|--------------------------------------------------------------------------
| Admin Portal Authentication
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:admin-login')->name('admin.login.submit');
Route::get('/admin/register', [AuthController::class, 'showRegister'])->name('admin.register');
Route::post('/admin/register', [AuthController::class, 'register'])->middleware('throttle:admin-login')->name('admin.register.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Universal Auth Aliases
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:admin-login')->name('register.submit');

// 2FA Verification Endpoints
Route::get('/admin/two-factor', [AuthController::class, 'showTwoFactor'])->name('admin.two-factor');
Route::post('/admin/two-factor', [AuthController::class, 'verifyTwoFactor'])->middleware('throttle:admin-two-factor')->name('admin.two-factor.verify');
Route::post('/admin/two-factor/resend', [AuthController::class, 'resendTwoFactor'])->middleware('throttle:admin-two-factor-resend')->name('admin.two-factor.resend');

// Lockscreen Endpoints
Route::get('/admin/lockscreen', [AuthController::class, 'lockscreen'])->name('admin.lockscreen');
Route::post('/admin/unlock', [AuthController::class, 'unlock'])->middleware('throttle:admin-login')->name('admin.unlock');

// Password Reset Endpoints
Route::get('/admin/forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.password.request');
Route::post('/admin/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:admin-two-factor-resend')->name('admin.password.email');
Route::get('/admin/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('admin.password.reset');
Route::post('/admin/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');

/*
|--------------------------------------------------------------------------
| Protected Admin Suite
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.view');
    Route::get('/search', [DashboardController::class, 'search'])->name('search');

    // Posts Management
    Route::resource('posts', PostController::class)->except(['show']);
    Route::get('posts/{post}/preview', [PostController::class, 'preview'])->name('posts.preview');
    Route::get('posts/{post}/revisions', [PostController::class, 'revisions'])->name('posts.revisions');
    Route::post('posts/{post}/revisions/{revision}/restore', [PostController::class, 'restoreRevision'])->name('posts.revisions.restore');
    Route::post('posts/{post}/toggle-status', [PostController::class, 'toggleStatus'])->name('posts.toggleStatus');
    Route::post('posts/{post}/clone', [PostController::class, 'clone'])->name('posts.clone');
    Route::delete('posts/gallery-images/{image}', [PostController::class, 'deleteGalleryImage'])->name('posts.gallery.destroy');

    // Taxonomies
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('tags', TagController::class)->except(['create', 'show', 'edit']);

    // Comments & Discourse CRUD
    Route::post('comments/bulk', [CommentController::class, 'bulkAction'])->name('comments.bulk');
    Route::post('comments/{id}/restore', [CommentController::class, 'restore'])->name('comments.restore');
    Route::delete('comments/{id}/force', [CommentController::class, 'forceDelete'])->name('comments.forceDelete');
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/spam', [CommentController::class, 'spam'])->name('comments.spam');

    // Comment Replies CRUD
    Route::get('comments/reply/{id}/edit', [CommentController::class, 'editReply'])->name('comments.reply.edit');
    Route::put('comments/reply/{id}', [CommentController::class, 'updateReply'])->name('comments.reply.update');
    Route::delete('comments/reply/{id}', [CommentController::class, 'destroyReply'])->name('comments.reply.destroy');
    Route::post('comments/reply/{id}/restore', [CommentController::class, 'restoreReply'])->name('comments.reply.restore');
    Route::delete('comments/reply/{id}/force', [CommentController::class, 'forceDeleteReply'])->name('comments.reply.forceDelete');

    Route::resource('comments', CommentController::class);

    // Static Pages Management
    Route::resource('pages', PageController::class)->except(['show']);

    // 301/302 Redirect Manager
    Route::resource('redirects', RedirectController::class)->except(['create', 'show', 'edit']);

    // 404 Error Monitor & Logs
    Route::get('not-found-logs', [NotFoundLogController::class, 'index'])->name('not-found-logs.index');
    Route::delete('not-found-logs/{log}', [NotFoundLogController::class, 'destroy'])->name('not-found-logs.destroy');
    Route::post('not-found-logs/clear', [NotFoundLogController::class, 'clear'])->name('not-found-logs.clear');

    // Media Manager
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::post('media/{id}/restore', [MediaController::class, 'restore'])->name('media.restore');
    Route::delete('media/{id}/force-delete', [MediaController::class, 'forceDelete'])->name('media.force-delete');
    Route::post('media/bulk-destroy', [MediaController::class, 'bulkDestroy'])->name('media.bulk-destroy');
    Route::post('media/bulk-restore', [MediaController::class, 'bulkRestore'])->name('media.bulk-restore');
    Route::post('media/bulk-force-delete', [MediaController::class, 'bulkForceDelete'])->name('media.bulk-force-delete');

    // Gallery Management
    Route::resource('galleries', GalleryController::class)->except(['create', 'edit']);
    Route::post('galleries/{id}/restore', [GalleryController::class, 'restore'])->name('galleries.restore');
    Route::delete('galleries/{id}/force-delete', [GalleryController::class, 'forceDelete'])->name('galleries.force-delete');
    Route::post('galleries/{gallery}/items', [GalleryController::class, 'addItems'])->name('galleries.items.store');
    Route::post('galleries/{gallery}/items/{item}/cover', [GalleryController::class, 'setCover'])->name('galleries.items.cover');
    Route::delete('galleries/{gallery}/items/{item}', [GalleryController::class, 'destroyItem'])->name('galleries.items.destroy');

    // Monetization: Ads & Transactions
    Route::resource('ads', AdvertisementController::class)->except(['create', 'show', 'edit']);
    Route::post('ads/{ad}/toggle', [AdvertisementController::class, 'toggle'])->name('ads.toggle');
    Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'update', 'destroy']);

    // Enhanced Subscriber Management System
    Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('subscribers', [SubscriberController::class, 'store'])->name('subscribers.store');
    Route::put('subscribers/{subscriber}', [SubscriberController::class, 'update'])->name('subscribers.update');
    Route::delete('subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');
    Route::post('subscribers/bulk', [SubscriberController::class, 'bulk'])->name('subscribers.bulk');
    Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
    Route::post('subscribers/import', [SubscriberController::class, 'import'])->name('subscribers.import');
    Route::post('subscribers/broadcast', [SubscriberController::class, 'broadcast'])->name('subscribers.broadcast');

    // Services Offered & Client Inquiries System
    Route::get('services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('services/create', [AdminServiceController::class, 'create'])->name('services.create');
    Route::post('services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::get('services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
    Route::put('services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('services/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::post('services/{service}/toggle-featured', [AdminServiceController::class, 'toggleFeatured'])->name('services.toggle-featured');
    Route::get('services/inquiries', [AdminServiceController::class, 'inquiries'])->name('services.inquiries');
    Route::put('services/inquiries/{inquiry}', [AdminServiceController::class, 'updateInquiry'])->name('services.inquiries.update');
    Route::delete('services/inquiries/{inquiry}', [AdminServiceController::class, 'destroyInquiry'])->name('services.inquiries.destroy');

    // Audience Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Menu Builder
    Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
    Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
    Route::post('menus/{menu}/items', [MenuController::class, 'addItem'])->name('menus.items.add');
    Route::delete('menus/items/{item}', [MenuController::class, 'deleteItem'])->name('menus.items.delete');

    // Widgets Manager
    Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::post('widgets', [WidgetController::class, 'store'])->name('widgets.store');
    Route::post('widgets/{widget}/toggle', [WidgetController::class, 'toggle'])->name('widgets.toggle');
    Route::delete('widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');

    // Master SEO Studio (Analysis View)
    Route::get('seo', [SEOController::class, 'index'])->name('seo.index');

    // Audit & Activity Logs (Read-only View)
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');

    // System Administration Vault (Restricted Exclusively to Administrator Role)
    Route::middleware('role:admin')->group(function () {
        // Settings & Maintenance
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/maintenance/toggle', [SettingController::class, 'toggleMaintenance'])->name('settings.maintenance.toggle');
        Route::post('settings/integrations', [SettingController::class, 'syncIntegrations'])->name('settings.integrations');

        // Backup Vault
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');

        // Master SEO Configuration
        Route::post('seo/robots', [SEOController::class, 'updateRobots'])->name('seo.update-robots');
        Route::post('seo/regenerate-sitemap', [SEOController::class, 'regenerateSitemap'])->name('seo.regenerate-sitemap');

        // Role & Permission Matrix
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles/{user}/assign', [RoleController::class, 'assign'])->name('roles.assign');
        Route::post('roles/permissions/toggle', [RoleController::class, 'togglePermission'])->name('roles.permissions.toggle');

        // User Management CRUD
        Route::post('users/bulk', [UserController::class, 'bulkAction'])->name('users.bulk');
        Route::get('users/{id}/duplicate', [UserController::class, 'duplicate'])->name('users.duplicate');
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force', [UserController::class, 'forceDelete'])->name('users.forceDelete');
        Route::post('users/update-role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::post('users/update-permission', [UserController::class, 'updatePermission'])->name('users.update-permission');
        Route::get('users/roles-matrix', [UserController::class, 'rolePermissions'])->name('users.roles-matrix');
        Route::resource('users', UserController::class);

        // Activity Log Purge Controls
        Route::post('activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
        Route::delete('activity-logs/{activity_log}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
    });

    // Staff Profile & Preferences
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::delete('profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/two-factor', [ProfileController::class, 'toggleTwoFactor'])->name('profile.two-factor');
    Route::post('profile/other-sessions', [ProfileController::class, 'destroyOtherSessions'])->name('profile.other-sessions');

    // Editorial Salon & Live Chat
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('chat/send', [ChatController::class, 'send'])->name('chat.send');

    // Contact Inquiries
    Route::get('contacts', [ContactInquiryController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [ContactInquiryController::class, 'show'])->name('contacts.show');
    Route::post('contacts/{contact}/read', [ContactInquiryController::class, 'toggleRead'])->name('contacts.read');
    Route::post('contacts/{contact}/toggle-read', [ContactInquiryController::class, 'toggleRead'])->name('contacts.toggle-read');
    Route::post('contacts/{contact}/reply', [ContactInquiryController::class, 'reply'])->name('contacts.reply');
    Route::delete('contacts/{contact}', [ContactInquiryController::class, 'destroy'])->name('contacts.destroy');
    Route::post('contacts/bulk', [ContactInquiryController::class, 'bulk'])->name('contacts.bulk');

    // Webmail & Editorial Dispatches
    Route::get('emails', [EmailController::class, 'index'])->name('emails.index');
    Route::get('emails/compose', [EmailController::class, 'compose'])->name('emails.compose');
    Route::post('emails/send', [EmailController::class, 'send'])->name('emails.send');
    Route::post('emails/bulk', [EmailController::class, 'bulk'])->name('emails.bulk');
    Route::get('emails/{email}', [EmailController::class, 'show'])->name('emails.show');
    Route::post('emails/{email}/star', [EmailController::class, 'toggleStar'])->name('emails.star');
    Route::post('emails/{email}/read', [EmailController::class, 'toggleRead'])->name('emails.read');
    Route::post('emails/{email}/trash', [EmailController::class, 'moveToTrash'])->name('emails.trash');
    Route::post('emails/{email}/restore', [EmailController::class, 'restore'])->name('emails.restore');
    Route::delete('emails/{email}', [EmailController::class, 'destroy'])->name('emails.destroy');

    // Calendar & Schedules
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::post('calendar/events', [CalendarController::class, 'store'])->name('calendar.store');
    Route::put('calendar/events/{event}', [CalendarController::class, 'update'])->name('calendar.update');
    Route::delete('calendar/events/{event}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

    // Task Manager (Architect.OS)
    Route::get('task-manager', [TaskManagerController::class, 'index'])->name('task-manager.index');
    Route::post('task-manager', [TaskManagerController::class, 'store'])->name('task-manager.store');
    Route::put('task-manager/{task}/status', [TaskManagerController::class, 'updateStatus'])->name('task-manager.update-status');
    Route::delete('task-manager/{task}', [TaskManagerController::class, 'destroy'])->name('task-manager.destroy');

    // File Manager (Elite Cloud Drive)
    Route::get('file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::post('file-manager/upload', [FileManagerController::class, 'uploadFile'])->name('file-manager.upload');
    Route::delete('file-manager/file/{id}', [FileManagerController::class, 'deleteFile'])->name('file-manager.file.delete');
    Route::post('file-manager/file/{id}/restore', [FileManagerController::class, 'restoreFile'])->name('file-manager.file.restore');
    Route::delete('file-manager/file/{id}/force', [FileManagerController::class, 'forceDeleteFile'])->name('file-manager.file.forceDelete');
    Route::post('file-manager/folder', [FileManagerController::class, 'createFolder'])->name('file-manager.folder');
    Route::put('file-manager/folder/{id}', [FileManagerController::class, 'renameFolder'])->name('file-manager.folder.rename');
    Route::delete('file-manager/folder/{id}', [FileManagerController::class, 'deleteFolder'])->name('file-manager.folder.delete');
    Route::post('file-manager/folder/{id}/restore', [FileManagerController::class, 'restoreFolder'])->name('file-manager.folder.restore');
    Route::delete('file-manager/folder/{id}/force', [FileManagerController::class, 'forceDeleteFolder'])->name('file-manager.folder.forceDelete');

    // Image Cropper Studio
    Route::get('image-cropper', [ImageCropperController::class, 'index'])->name('image-cropper.index');

    // Error Templates & Preview Showcase
    Route::prefix('errors')->name('errors.')->group(function () {
        Route::get('/', fn() => view('admin.tools.errors'))->name('index');
        Route::get('400', fn() => response()->view('errors.400', [], 400))->name('400');
        Route::get('403', fn() => response()->view('errors.403', [], 403))->name('403');
        Route::get('404', fn() => response()->view('errors.404', [], 404))->name('404');
        Route::get('500', fn() => response()->view('errors.500', [], 500))->name('500');
        Route::get('503', fn() => response()->view('errors.503', [], 503))->name('503');
        Route::get('maintenance', fn() => response()->view('errors.maintenance', [], 503))->name('maintenance');
    });
});
