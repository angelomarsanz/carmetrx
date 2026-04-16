<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventRequestsForDeactivatedAccount
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
        $userInfo = Auth::guard('customer')->user();

        if ($userInfo->status == 0) {
            Auth::guard('customer')->logout();

            session()->flash('error', 'Sorry, your account has been deactivated!');

            return redirect()->route('frontend.user.login');
        }

        return $next($request);
    }
}
