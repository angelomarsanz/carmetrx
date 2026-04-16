<?php

namespace App\Http\Middleware;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User;
use App\Models\User\UserPermission;
use Closure;
use Illuminate\Http\Request;

class RouteAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $page)
    {
        $user = getUser();
        $packagePermissions = UserPermissionHelper::packagePermission($user->id);
        $packagePermissions = json_decode($packagePermissions, true);

        if (!in_array($page, $packagePermissions)) {
            return redirect()->route('frontend.user.index', getParam());
        }

        return $next($request);
    }
}
