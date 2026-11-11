<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware {
    public function handle(Request $request, Closure $next, string ...$permissions): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized atelier clearance: Required permission [' . implode(', ', $permissions) . '] is missing.');
    }
}
