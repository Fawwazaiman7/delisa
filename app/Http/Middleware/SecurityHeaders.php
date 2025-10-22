<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $isLocal = app()->environment('local');

        // COMMON
        $script      = ["'self'"];
        $scriptElem  = ["'self'"];
        $style       = ["'self'"];
        $styleElem   = ["'self'"];
        $font        = ["'self'"];
        $img         = ["'self'", 'data:', 'blob:'];
        $connect     = ["'self'"];

        if ($isLocal) {
            $viteHttp = ['http://localhost:5173', 'http://127.0.0.1:5173'];
            $viteWs   = ['ws://localhost:5173', 'ws://127.0.0.1:5173'];

            $script      = array_merge($script, $viteHttp);
            $scriptElem  = array_merge($scriptElem, $viteHttp);
            $styleElem   = array_merge($styleElem, $viteHttp);
            $font        = array_merge($font, $viteHttp);
            $connect     = array_merge($connect, $viteHttp, $viteWs);
            $img         = array_merge($img, ['http://localhost:8000', 'http://127.0.0.1:8000']);
        }

        $storageOrigins = $this->extractOrigins([
            config('filesystems.disks.public.url'),
            config('filesystems.disks.s3.url'),
            config('filesystems.disks.s3.endpoint')
                ? config('filesystems.disks.s3.endpoint')
                : null,
        ]);

        if ($storageOrigins) {
            $img = array_merge($img, $storageOrigins);
        }

        $script     = array_values(array_unique($script));
        $scriptElem = array_values(array_unique($scriptElem));
        $style      = array_values(array_unique($style));
        $styleElem  = array_values(array_unique($styleElem));
        $font       = array_values(array_unique($font));
        $img        = array_values(array_unique($img));
        $connect    = array_values(array_unique($connect));

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "script-src "       . implode(' ', $script),
            "script-src-elem "  . implode(' ', $scriptElem),
            "style-src "        . implode(' ', $style),
            "style-src-elem "   . implode(' ', $styleElem),
            "font-src "         . implode(' ', $font),
            "img-src "          . implode(' ', $img),
            "connect-src "      . implode(' ', $connect),
            "upgrade-insecure-requests",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $directives));
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $response->headers->remove('X-Powered-By');
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        return $response;
    }

    /**
     * @param  array<int, string|null>  $urls
     * @return array<int, string>
     */
    private function extractOrigins(array $urls): array
    {
        $origins = [];

        foreach ($urls as $url) {
            if (! $url) {
                continue;
            }

            $parts = parse_url($url);
            if (! $parts || empty($parts['scheme']) || empty($parts['host'])) {
                continue;
            }

            $origin = rtrim($parts['scheme'].'://'.$parts['host'], '/');
            if (! empty($parts['port'])) {
                $origin .= ':'.$parts['port'];
            }

            $origins[$origin] = $origin;
        }

        return array_values($origins);
    }
}
