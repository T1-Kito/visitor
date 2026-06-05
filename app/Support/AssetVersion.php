<?php

namespace App\Support;

final class AssetVersion
{
    public static function url(string $path): string
    {
        $path = ltrim($path, '/');
        $url = asset($path);
        $absolutePath = public_path($path);

        if (! is_file($absolutePath)) {
            return $url;
        }

        return $url.'?v='.filemtime($absolutePath);
    }
}
