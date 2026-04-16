<?php

namespace App\Http\Middleware;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\BasicSetting;
use Closure;
use Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class UserMaintenance
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

        $userId = getUser()->id;

        // get user basic settings as per website 
        $basicSetting = BasicSetting::where('user_id', $userId)->first();
        $maintenanceStatus = $basicSetting->maintenance_status ?? 0;
        if ($maintenanceStatus == 1) {
            $token = $basicSetting->bypass_token;
            if (session()->has('user-bypass-token') && session()->get('user-bypass-token') == $token) {
                return $next($request);
            }
            $data['userBs'] = $basicSetting;
            return response()->view('errors.user-503', $data);
        }


        return $next($request);
    }
}
