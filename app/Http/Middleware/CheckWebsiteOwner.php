<?php

namespace App\Http\Middleware;

use App\Http\Helpers\UserPermissionHelper;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckWebsiteOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // this is only for path based URL

        $author = getUser();
        if (!empty($author)) {
            // if the 'Website Owner' of the 'Authenticated User' does not match with the 'Retrieved Owner', then redirect to home
            if (Auth::guard('customer')->check() && Auth::guard('customer')->user()->user->username != $author->username) {
                return redirect()->route('frontend.user.dashboard', Auth::guard('customer')->user()->user->username);
            }
        }
        return $next($request);
    }
}
