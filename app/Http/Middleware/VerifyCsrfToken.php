<?php

namespace App\Http\Middleware;

class VerifyCsrfToken extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
{
    /**
     * Disable the public XSRF-TOKEN cookie so that it isn't exposed to
     * third-party scripts. Blade based forms continue to work because the CSRF
     * token is embedded directly in the markup.
     */
    protected $addHttpCookie = false;
}

