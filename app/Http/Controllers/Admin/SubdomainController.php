<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\UserSubdomain;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubdomainController extends Controller
{
    public function index(Request $request)
    {

        $subdomains = UserSubdomain::get();
        $userIds = [];
        foreach ($subdomains as $subdomain) {
            if (cPackageHasSubdomain($subdomain->user)) {
                $userIds[] = $subdomain->user->id;
            }
        }

        $type = $request->type;
        $websitename = $request->username;
        $subdomains = UserSubdomain::whereHas('memberships', function ($q) {
            $q->where('status', '=', 1)
                ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
        })->when($type, function ($query, $type) {
            if ($type == 'pending') {
                return $query->where('status', 0);
            } elseif ($type == 'connected') {
                return $query->where('status', 1);
            }
        })->when($websitename, function ($query, $websitename) {
            return $query->where('requested_subdomain', 'LIKE', '%' . $websitename . '%');
        })->when(!empty($userIds), function ($query) use ($userIds) {
            return $query->whereIn('user_id', $userIds);
        })->latest()->paginate(10);

        $data['subdomains'] = $subdomains;
        return view('admin.subdomains.index', $data);
    }

    public function status(Request $request)
    {
        $subdomain = UserSubdomain::findOrFail($request->id);
        if ($request->status == 1) {
            $sDomain = UserSubdomain::where('website_id', $subdomain->website->id)->where('status', 1)->get();

            if ($sDomain->count() > 0) {
                return back()->with('warning', __("This website already has a subdomain, Please remove user's connected subdomain first."));
            }
        }
        $subdomain->status = $request->status;
        $subdomain->save();

        session()->flash('success', __('Updated successfully!'));
        return back();
    }
}
