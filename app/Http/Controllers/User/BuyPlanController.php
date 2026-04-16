<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UserPermissionHelper;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Models\Membership;
use App\Models\OfflineGateway;
use App\Models\Package;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BuyPlanController extends Controller
{
    use TenantFrontendLanguage;

    public function index(Request $request)
    {

        $tenantId = Auth::guard('web')->user()->id;
        $currentLang = $this->currentLang($tenantId);

        $data['bex'] = $currentLang->basic_extended;
        $data['packages'] = Package::where('status', '1')->get();

        $nextPackageCount = Membership::query()->where([
            ['user_id', Auth::id()],
            ['expire_date', '>=', Carbon::now()->toDateString()]
        ])->whereYear('start_date', '<>', '9999')->where('status', '<>', 2)->count();
        //current package
        $data['current_membership'] = Membership::query()->where([
            ['user_id', Auth::id()],
            ['start_date', '<=', Carbon::now()->toDateString()],
            ['expire_date', '>=', Carbon::now()->toDateString()]
        ])->where('status', 1)->whereYear('start_date', '<>', '9999')->first();
        if ($data['current_membership']) {
            $countCurrMem = Membership::query()->where([
                ['user_id', Auth::id()],
                ['start_date', '<=', Carbon::now()->toDateString()],
                ['expire_date', '>=', Carbon::now()->toDateString()]
            ])->where('status', '<>', 2)->whereYear('start_date', '<>', '9999')->count();
            if ($countCurrMem > 1) {
                $data['next_membership'] = Membership::query()->where([
                    ['user_id', Auth::id()],
                    ['start_date', '<=', Carbon::now()->toDateString()],
                    ['expire_date', '>=', Carbon::now()->toDateString()]
                ])->where('status', '<>', 2)->whereYear('start_date', '<>', '9999')->orderBy('id', 'DESC')->first();
            } else {
                $data['next_membership'] = Membership::query()->where([
                    ['user_id', Auth::id()],
                    ['start_date', '>', $data['current_membership']->expire_date]
                ])->whereYear('start_date', '<>', '9999')->where('status', '<>', 2)->first();
            }
            $data['next_package'] = $data['next_membership'] ? Package::query()->where('id', $data['next_membership']->package_id)->first() : null;
        }
        $data['current_package'] = $data['current_membership'] ? Package::query()->where('id', $data['current_membership']->package_id)->first() : null;
        $data['package_count'] = $nextPackageCount;

        return view('user.buy-plan.index', $data);
    }

    public function checkout($package_id)
    {
        $packageCount = Membership::where([
            ['user_id', Auth::id()],
            ['expire_date', '>=', Carbon::now()->toDateString()]
        ])->whereYear('start_date', '<>', '9999')->where('status', '<>', 2)->count();

        $hasPendingMemb = UserPermissionHelper::hasPendingMembership(Auth::id());


        if ($hasPendingMemb) {
            Session::flash('warning', __('You already have a Pending Membership Request'));
            return back();
        }
        if ($packageCount >= 2) {
            Session::flash('warning', __('You have another package to activate after the current package expires') . '. '. __('You cannot purchase / extend any package, until the next package is activated') . '.');
            return back();
        }

        $tenantId = Auth::guard('web')->user()->id;
        $currentLang = $this->currentLang($tenantId);
        $be = $currentLang->basic_extended;
        $online = PaymentGateway::where('status', 1)->get();
        $offline = OfflineGateway::all();
        $data['offline'] = $offline;
        $data['payment_methods'] = $online->merge($offline);
        $data['package'] = Package::findOrFail($package_id);
        $data['membership'] = Membership::where([
            ['user_id', Auth::id()],
            ['expire_date', '>=', \Carbon\Carbon::now()->format('Y-m-d')]
        ])->where('status', '<>', 2)->whereYear('start_date', '<>', '9999')
            ->latest()
            ->first();
        $data['previousPackage'] = null;
        if (!is_null($data['membership'])) {
            $data['previousPackage'] = Package::query()
                ->where('id', $data['membership']->package_id)
                ->first();
        }
        $data['bex'] = $be;
        return view('user.buy-plan.checkout', $data);
    }
}
