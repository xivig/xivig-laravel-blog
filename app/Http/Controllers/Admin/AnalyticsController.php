<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Post;
use App\Models\SearchLog;
use App\Models\Visitor;

class AnalyticsController extends Controller {
    public function index() {
        $totalVisitors = Visitor::count();
        $totalPageViews = PageView::count();
        $recentVisitors = Visitor::latest('last_seen_at')->take(10)->get();

        $topPages = PageView::selectRaw('path, count(*) as views')
            ->groupBy('path')
            ->orderByDesc('views')
            ->take(10)
            ->get();

        $devices = Visitor::selectRaw('device, count(*) as count')
            ->groupBy('device')
            ->pluck('count', 'device')
            ->toArray();

        $browsers = Visitor::selectRaw('browser, count(*) as count')
            ->groupBy('browser')
            ->pluck('count', 'browser')
            ->toArray();

        $popularSearches = SearchLog::selectRaw('query, count(*) as searches, sum(results_count) as total_results')
            ->groupBy('query')
            ->orderByDesc('searches')
            ->take(10)
            ->get();

        $topChronicles = Post::published()->orderByDesc('views_count')->take(5)->get();

        return view('admin.analytics.index', compact(
            'totalVisitors',
            'totalPageViews',
            'recentVisitors',
            'topPages',
            'devices',
            'browsers',
            'popularSearches',
            'topChronicles'
        ));
    }
}
