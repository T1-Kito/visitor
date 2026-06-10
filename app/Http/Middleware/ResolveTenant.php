<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable('tenants')) {
            return $next($request);
        }

        $tenantId = $request->user()?->tenant_id;

        if ($tenantId === null) {
            $host = strtolower((string) $request->getHost());
            $tenantId = Tenant::query()
                ->where('domain', $host)
                ->where('status', 'active')
                ->value('id');
        }

        if ($tenantId === null) {
            $tenantId = Tenant::query()
                ->where('slug', config('saas.default_tenant_slug', 'default'))
                ->value('id');
        }

        app(TenantContext::class)->set($tenantId === null ? null : (int) $tenantId);

        return $next($request);
    }
}
