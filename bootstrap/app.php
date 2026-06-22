<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use App\Http\Middleware\EnsureLicensed;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\RedirectToCanonicalUrl;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\RestrictPublicRegistrationPort;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(RedirectToCanonicalUrl::class);
        $middleware->append(ResolveTenant::class);
        $middleware->append(RestrictPublicRegistrationPort::class);
        $middleware->append(EnsureLicensed::class);

        $middleware->alias([
            'permission' => EnsurePermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->is('login') && auth()->check()) {
                return redirect()->route('admin.dashboard');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.',
                ], 419);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        });
    })->create();
