<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\UserSubdomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubdomainController extends Controller
{
    public function subdomain()
    {
        $userId = Auth::guard('web')->user()->id;
        $features = UserPermissionHelper::packagePermission($userId);
        $data['features'] = json_decode($features, true);
        $data['subdomain'] = UserSubdomain::where('user_id', Auth::guard('web')->user()->id)->orderBy('id', 'DESC')->first();
        return view('user.subdomain', $data);
    }

    public function subdomainrequest($subdomainName)
    {
        $subdomain = new UserSubdomain();
        $subdomain->user_id = Auth::guard('web')->user()->id;
        $subdomain->requested_subdomain = $subdomainName;
        $subdomain->status = 0;
        $subdomain->save();

        return;
    }
}
