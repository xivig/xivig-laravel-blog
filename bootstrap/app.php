<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\RedirectMiddleware;
use App\Http\Middleware\TrackVisitorMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Models\NotFoundLog;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->append(RedirectMiddleware::class);
        $middleware->web(append: [
            TrackVisitorMiddleware::class,
            \App\Http\Middleware\CheckSiteMaintenance::class,
        ]);
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuthMiddleware::class,
            'role'       => RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded photographs exceed the server payload limit.'
                ], 413);
            }
            return back()->with('error', 'The uploaded files exceed the server upload limit. Please upload smaller images or fewer photos per batch.');
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            try {
                NotFoundLog::logUrl(
                    $request->fullUrl(),
                    $request->header('referer'),
                    $request->ip(),
                    $request->userAgent()
                );
            } catch (\Throwable $t) {
                // Ignore database errors during 404 logging
            }
        });
    })->create();
