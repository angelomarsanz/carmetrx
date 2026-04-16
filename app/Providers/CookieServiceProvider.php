<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

class CookieServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        View::composer('tenant_frontend.cookie-alert.index', function ($view) {

            $cookieConsentConfig = config('cookie');
            $alreadyConsentedWithCookies = Cookie::has($cookieConsentConfig['tenant_cookie_name']);

            $parsedUrl = parse_url(url()->current());

            $host =  $parsedUrl['host'];
            $path = 1;

            if ($host == env('WEBSITE_HOST')) {
                $path = 0;
            }


            $view->with(compact('alreadyConsentedWithCookies', 'cookieConsentConfig', 'path'));
        });



        View::composer('front.cookie-alert.index', function ($view) {
            $cookieConsentConfig = config('cookie');

            $alreadyConsentedWithCookies = Cookie::has($cookieConsentConfig['cookie_name']);

            $view->with(compact('alreadyConsentedWithCookies', 'cookieConsentConfig'));
        });

        $this->app->resolving(EncryptCookies::class, function (EncryptCookies $encryptCookies) {
            $encryptCookies->disableFor(config('cookie.cookie_name'));
            $encryptCookies->disableFor(config('cookie.tenant_cookie_name'));
        });
    }
}
