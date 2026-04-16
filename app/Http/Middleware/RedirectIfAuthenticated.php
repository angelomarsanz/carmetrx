<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard) {
            case 'admin':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('admin.dashboard');
                }
                break;
            case 'customer':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('frontend.user.dashboard', getParam());
                }
                break;
            case 'agent':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('agent.dashboard', getParam());
                }
                break;
            default:
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('user-dashboard');
                }
                break;
        }

        return $next($request);
    }
}
