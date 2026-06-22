<?php

namespace App\Support;

use Illuminate\Http\Request;

class PublicRegistrationAccess
{
    public static function isPublicPortRequest(Request $request): bool
    {
        if (! (bool) config('public-registration.enabled', true)) {
            return false;
        }

        $publicPort = (int) config('public-registration.port', 8443);
        $hostHeader = (string) $request->server('HTTP_HOST', '');
        $requestPort = null;

        if (preg_match('/:(\d+)$/', $hostHeader, $matches)) {
            $requestPort = (int) $matches[1];
        } elseif (is_numeric($request->server('SERVER_PORT'))) {
            $requestPort = (int) $request->server('SERVER_PORT');
        }

        return $publicPort > 0 && $requestPort === $publicPort;
    }
}