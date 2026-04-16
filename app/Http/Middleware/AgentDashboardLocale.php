<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\Tenant\TenantLanguage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentDashboardLocale
{
    use TenantLanguage;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentLang = $this->agentCurrentLang();
        session()->put('agent_dashboard_lang', $currentLang->code);
        app()->setLocale('tenant_' . $currentLang->code);
        return $next($request);
    }
}
