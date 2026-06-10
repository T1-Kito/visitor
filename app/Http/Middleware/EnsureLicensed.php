<?php

namespace App\Http\Middleware;

use App\Support\LicenseManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLicensed
{
    public function __construct(private readonly LicenseManager $licenses)
    {
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request) || $this->licenses->isValid()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Hệ thống chưa được kích hoạt bản quyền.',
                'device_id' => $this->licenses->deviceId(),
            ], 423);
        }

        return redirect()->route('license.show');
    }

    private function shouldSkip(Request $request): bool
    {
        if (! $this->licenses->isEnforced()) {
            return true;
        }

        return $request->routeIs('license.*')
            || $request->is('license')
            || $request->routeIs('login')
            || $request->is('login')
            || $request->routeIs('login.attempt')
            || $request->routeIs('csrf-token')
            || $request->is('csrf-token')
            || $request->routeIs('admin.logout')
            || $request->is('up');
    }
}
