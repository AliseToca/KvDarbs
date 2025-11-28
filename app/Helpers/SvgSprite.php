<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Vite;

class SvgSprite
{
    public static function getSpriteUrl(): string
    {
        if (self::isViteDevRunning()) {
            return self::getDevSpriteUrl();
        }
        $buildDir = config('vite.build_path', 'front');
        $manifestPath = public_path($buildDir . '/manifest.json');

        if (!is_file($manifestPath)) {
            $fallback = asset('front/svg/sprite.svg');
            return $fallback;
        }

        return Vite::asset('resources/assets/svg/sprite.svg');
    }

    private static function isViteDevRunning(): bool {
        return app()->environment('local') && file_exists(public_path('hot'));
    }

    private static function getDevSpriteUrl(): string {
        $publicPath = public_path('front/svg/sprite.svg');

        // Add timestamp for cache busting
        $hash = file_exists($publicPath) ? filemtime($publicPath) : time();
        return asset('front/svg/sprite.svg') . '?v=' . $hash;
    }
}
