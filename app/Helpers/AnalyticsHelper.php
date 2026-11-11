<?php

namespace App\Helpers;

class AnalyticsHelper {
    public static function detectDevice(?string $userAgent): string {
        $ua = strtolower($userAgent ?? '');
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'Mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'Tablet';
        }
        return 'Desktop';
    }

    public static function detectBrowser(?string $userAgent): string {
        $ua = strtolower($userAgent ?? '');
        if (str_contains($ua, 'edg')) return 'Edge';
        if (str_contains($ua, 'chrome')) return 'Chrome';
        if (str_contains($ua, 'safari') && !str_contains($ua, 'chrome')) return 'Safari';
        if (str_contains($ua, 'firefox')) return 'Firefox';
        if (str_contains($ua, 'opera') || str_contains($ua, 'opr')) return 'Opera';
        return 'Other';
    }
}
