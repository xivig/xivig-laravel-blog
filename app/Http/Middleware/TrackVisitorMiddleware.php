<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $response = $next($request);

        // Track only successful public GET page loads
        if ($request->isMethod('GET') && !$request->is('admin*') && !$request->is('api*') && $response->getStatusCode() === 200) {
            AnalyticsService::track($request);
        }

        return $response;
    }
}
