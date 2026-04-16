<?php

namespace App\Http\Middleware;

use App\Models\User\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;

class TenantFrontendLocale
{
    use  TenantFrontendLanguage;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('tenant_frontend_lang'); // Check if the session already has the language

        $user = getUser();
        if (!$locale) {
            // Fetch the Tenant's default language if not already in session
            $defaultLanguage =  $this->defaultLang($user->id);

            if ($defaultLanguage) {
                $locale = $defaultLanguage->code;
                Session::put('tenant_frontend_lang', $defaultLanguage->code);
            }
        } else {
            $chekLanguage =  $this->selectLang($user->id, $locale);

            if (!$chekLanguage) {
                $defaultLang = $this->defaultLang($user->id);
                $locale = $defaultLang->code;
                Session::put('tenant_frontend_lang', $locale);
            }
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
