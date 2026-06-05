<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToCanonicalUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethodSafe()) {
            return $next($request);
        }

        $appUrl = (string) config('app.url');
        $targetHost = parse_url($appUrl, PHP_URL_HOST);
        $targetScheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'https';

        if (! $targetHost || $this->isLocalHost($targetHost) || $this->isLocalHost($request->getHost())) {
            return $next($request);
        }

        $currentHost = $request->getHost();
        $currentScheme = $request->getScheme();

        if ($currentHost !== $targetHost) {
            return redirect()->to($targetScheme.'://'.$targetHost.$request->getRequestUri(), 302);
        }

        if ($currentScheme !== $targetScheme && ! $request->headers->has('x-forwarded-proto')) {
            return redirect()->to($targetScheme.'://'.$targetHost.$request->getRequestUri(), 302);
        }

        return $next($request);
    }

    private function isLocalHost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local');
    }
}
