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
        $style       = ["'self'", "'unsafe-inline'"];
        $styleElem   = ["'self'", "'unsafe-inline'"];
        $font        = ["'self'"];
        $img         = ["'self'", 'data:'];
        $connect     = ["'self'"];

        if ($isLocal) {
            $viteHttp = ['http://localhost:5173', 'http://127.0.0.1:5173'];
            $viteWs   = ['ws://localhost:5173', 'ws://127.0.0.1:5173'];

            $script      = array_merge($script, $viteHttp);
            $scriptElem  = array_merge($scriptElem, $viteHttp);
            $styleElem   = array_merge($styleElem, $viteHttp);
            $font        = array_merge($font, $viteHttp);
            $connect     = array_merge($connect, $viteHttp, $viteWs);
        }

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
}
