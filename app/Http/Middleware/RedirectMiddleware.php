<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware {
    /**
     * Handle an incoming request for 301/302 Redirect Manager.
     */
    public function handle(Request $request, Closure $next): Response {
        $path = '/' . ltrim($request->path(), '/');

        // Check if this path matches an active redirect
        $redirect = Redirect::where('source_url', $path)
            ->where('is_active', true)
            ->first();

        if ($redirect) {
            $redirect->recordHit();
            return redirect($redirect->target_url, $redirect->status_code);
        }

        return $next($request);
    }
}
