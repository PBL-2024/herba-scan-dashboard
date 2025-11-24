<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustProxies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set trusted proxies based on environment
        if (config('app.env') === 'production' && env('TRUSTED_PROXIES')) {
            if (env('TRUSTED_PROXIES') === '*') {
                $request->setTrustedProxies(['*'], Request::HEADER_X_FORWARDED_ALL);
            } else {
                $trustedProxies = explode(',', env('TRUSTED_PROXIES'));
                $request->setTrustedProxies($trustedProxies, Request::HEADER_X_FORWARDED_ALL);
            }
        }

        // Force HTTPS detection when behind proxy
        if ($request->hasHeader('X-Forwarded-Proto') && $request->header('X-Forwarded-Proto') === 'https') {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);
        }

        return $next($request);
    }
}