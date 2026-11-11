<?php

namespace App\Services;

use App\Helpers\AnalyticsHelper;
use App\Models\PageView;
use App\Models\Visitor;
use Illuminate\Http\Request;

class AnalyticsService {
    public static function track(Request $request, ?string $viewableType = null, ?int $viewableId = null): void {
        try {
            $ip = $request->ip() ?? '127.0.0.1';
            $ua = $request->userAgent();

            $visitor = Visitor::firstOrCreate(
                ['ip_address' => $ip],
                [
                    'session_id'    => session()->getId(),
                    'device'        => AnalyticsHelper::detectDevice($ua),
                    'browser'       => AnalyticsHelper::detectBrowser($ua),
                    'country_code'  => 'FR',
                    'country_name'  => 'France',
                    'user_agent'    => mb_substr($ua ?? '', 0, 500),
                    'first_seen_at' => now(),
                    'last_seen_at'  => now(),
                ]
            );

            $visitor->update(['last_seen_at' => now()]);

            PageView::create([
                'visitor_id'    => $visitor->id,
                'url'           => $request->fullUrl(),
                'path'          => '/' . ltrim($request->path(), '/'),
                'viewable_type' => $viewableType,
                'viewable_id'   => $viewableId,
                'referrer'      => $request->header('referer'),
                'ip_address'    => $ip,
            ]);
        } catch (\Throwable $e) {
            // Non-blocking telemetry
        }
    }

    public static function getSummary(): array {
        return [
            'total_visitors'   => Visitor::count(),
            'total_page_views' => PageView::count(),
            'devices'          => Visitor::selectRaw('device, count(*) as count')->groupBy('device')->pluck('count', 'device')->toArray(),
            'browsers'         => Visitor::selectRaw('browser, count(*) as count')->groupBy('browser')->pluck('count', 'browser')->toArray(),
            'top_pages'        => PageView::selectRaw('path, count(*) as views')->groupBy('path')->orderByDesc('views')->take(7)->get(),
        ];
    }
}
