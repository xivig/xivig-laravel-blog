<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteMaintenance {
    public function handle(Request $request, Closure $next): Response {
        $maintenance = Setting::get('maintenance_mode', '0');

        if ($maintenance === '1') {
            // Bypass for admin suite and authentication routes
            if ($request->is('admin*') || $request->is('login*') || $request->is('logout*') || $request->is('assets*') || $request->is('build*') || $request->is('vendor*')) {
                return $next($request);
            }

            // Bypass for authenticated administrators / editors
            if (Auth::check() && in_array(Auth::user()->role, ['admin', 'super_admin', 'editor'])) {
                return $next($request);
            }

            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
