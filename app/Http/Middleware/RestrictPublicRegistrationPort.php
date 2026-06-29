<?php

namespace App\Http\Middleware;

use App\Support\PublicRegistrationAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictPublicRegistrationPort
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! PublicRegistrationAccess::isPublicPortRequest($request)) {
            return $next($request);
        }

        $allowedRequests = [
            'GET kiosk/register',
            'GET kiosk/privacy-notice',
            'GET kiosk/employees/search',
            'POST kiosk/checkin/manual',
        ];
        $requestSignature = strtoupper($request->method()).' '.$request->path();

        abort_unless(in_array($requestSignature, $allowedRequests, true), 404);

        return $next($request);
    }
}