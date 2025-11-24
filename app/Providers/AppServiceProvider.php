<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        // Configure trusted proxies for Nginx Proxy Manager
        $this->configureTrustedProxies();
    }

    /**
     * Configure trusted proxies for reverse proxy setups like Nginx Proxy Manager
     */
    protected function configureTrustedProxies(): void
    {
        // Trust all proxies if TRUSTED_PROXIES is set to *
        if (env('TRUSTED_PROXIES') === '*') {
            Request::setTrustedProxies(['*'], Request::HEADER_X_FORWARDED_ALL);
        } elseif (env('TRUSTED_PROXIES')) {
            // Trust specific proxy IPs
            $trustedProxies = explode(',', env('TRUSTED_PROXIES'));
            Request::setTrustedProxies($trustedProxies, Request::HEADER_X_FORWARDED_ALL);
        }

        // Trust hosts if configured
        if (env('TRUST_HOSTS', false)) {
            Request::setTrustedHosts([env('APP_URL'), '*.localhost', 'localhost']);
        }
    }
}
