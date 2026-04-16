<?php

namespace App\Http\Middleware;

use App\Traits\AdminLanguage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminLocale
{
    use AdminLanguage;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
     
        $currentLang = $this->currentLang();
        session()->put('admin_lang', $currentLang->code);
        app()->setLocale('admin_' . $currentLang->code);

        return $next($request);
    }
}
