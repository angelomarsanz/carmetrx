<?php

namespace App\Http\Middleware;

use App\Models\User\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ChangeLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (session()->has('user_language')) {
            $language = Language::where('id', session()->get('user_language'))->first();
            $locale = $language->code;
        }

        if (empty($locale)) {
            // set the default language as system locale 
            $user = getUser();
            $languageCode = Language::where('user_id', $user->id)->where('is_default', '=', 1)
                ->pluck('code')
                ->first();
            App::setLocale($languageCode);
        } else {
            // set the selected language as system locale
            App::setLocale($locale);
        }

        return $next($request);
    }
}
