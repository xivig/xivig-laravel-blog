<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware {
    /**
     * Handle an incoming request ensuring admin or editor credentials.
     */
    public function handle(Request $request, Closure $next): Response {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please authenticate to access the Maison Gazette administration suite.');
        }

        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'editor'])) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account credentials do not possess administrative access.');
        }

        return $next($request);
    }
}
