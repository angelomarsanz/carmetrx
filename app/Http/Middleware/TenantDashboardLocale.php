<?php

namespace App\Http\Middleware;

use App\Traits\Tenant\TenantLanguage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantDashboardLocale
{
    use TenantLanguage;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentLang = $this->currentLang();
        session()->put('tenant_dashboard_lang', $currentLang->code);
        app()->setLocale('tenant_' . $currentLang->code);
        
        return $next($request);
    }
}
