<?php

namespace App\Http\Controllers\User;

use App;
use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Package;
use App\Models\Customer;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\User\Follower;
use App\Models\User\Language;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User\Project\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User\Property\Property;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('setlang');
    }

    public function index()
    {
        $user = User::find(Auth::guard('web')->user()->id);
        $data['user'] = $user;

        $data['customers'] = $user->customers()->count();
        $data['agents'] = $user->agents()->count();
        $data['propertyMessages'] = $user->propertyContact()->count();
        $data['projectMessages'] = $user->projectContact()->count();
        $data['followers'] = Follower::where('following_id', $user->id)->count();
        $data['followings'] = Follower::where('follower_id', $user->id)->count();

        $data['memberships'] = Membership::query()->where('user_id', $user->id)->orderBy('id', 'DESC')->limit(10)->get();

        $data['users'] = [];
        $followingListIds = Follower::query()->where('follower_id', Auth::id())->pluck('following_id');
        if (count($followingListIds) > 0) {
            $data['users'] = User::whereIn('id', $followingListIds)->limit(10)->get();
        }

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


        $data['totalProperties'] = Property::where('user_id', $user->id)->count();
        $data['totalProjects'] = Project::where('user_id', $user->id)->count();

        $totalProperties = DB::table('user_properties')
            ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
            ->groupBy('month')
            ->where('user_id', $user->id)
            ->whereYear('created_at', '=', date('Y'))
            ->get();

        $totalProjects = DB::table('user_projects')
            ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
            ->groupBy('month')
            ->where('user_id', $user->id)
            ->whereYear('created_at', '=', date('Y'))
            ->get();

        $months = [];
        $totalPropertyArr = [];
        $totalProjectsArr = [];


        //event icome calculation
        for ($i = 1; $i <= 12; $i++) {
            // get all 12 months name
            $monthNum = $i;
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('M');
            array_push($months, $monthName);

            // get all 12 months's property posts
            $propertyFound = false;
            foreach ($totalProperties as $totalProperty) {
                if ($totalProperty->month == $i) {
                    $propertyFound = true;
                    array_push($totalPropertyArr, $totalProperty->total);
                    break;
                }
            }
            if ($propertyFound == false) {
                array_push($totalPropertyArr, 0);
            }

            // // get all 12 months's project post
            $projectFound = false;
            foreach ($totalProjects as $totalProject) {
                if ($totalProject->month == $i) {
                    $projectFound = true;
                    array_push($totalProjectsArr, $totalProject->total);
                    break;
                }
            }
            if ($projectFound == false) {
                array_push($totalProjectsArr, 0);
            }
        }

        $data['monthArr'] = $months;
        $data['totalPropertiesArr'] = $totalPropertyArr;
        $data['totalProjectsArr'] = $totalProjectsArr;

        return view('user.dashboard', $data);
    }

    public function status(Request $request)
    {
        $user = Auth::user();
        $user->online_status = $request->value;
        $user->save();
        $msg = '';
        if ($request->value == 1) {
            $msg = __('Profile has been made visible') . ".";
        } else {
            $msg = __('Profile has been hidden') . ".";
        }
        Session::flash('success', $msg);
        return "success";
    }

    public function profile()
    {
        $user = Auth::guard('web')->user();
        return view('user.edit-profile', compact('user'));
    }

    public function profileupdate(Request $request)
    {

        $img = $request->file('photo');
        $allowedExts = array('jpg', 'png', 'jpeg');
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'company_name' => 'required',
            'username' => 'required|unique:users,username,' . Auth::user()->id,
            'phone' => 'required',
            'country' => 'required',
            'photo' => [
                function ($attribute, $value, $fail) use ($request, $img, $allowedExts) {
                    if ($request->hasFile('photo')) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail(__('Only png, jpg, jpeg image is allowed') . ".");
                        }
                    }
                },
            ],
        ]);

        //--- Validation Section Ends
        $input = $request->all();
        if ($request->show_email_addresss) {
            $input['show_email_addresss'] = 1;
        } else {
            $input['show_email_addresss'] = 0;
        }
        if ($request->show_phone_number) {
            $input['show_phone_number'] = 1;
        } else {
            $input['show_phone_number'] = 0;
        }
        if ($request->show_contact_form) {
            $input['show_contact_form'] = 1;
        } else {
            $input['show_contact_form'] = 0;
        }
        if ($request->show_profile) {
            $input['show_profile'] = 1;
        } else {
            $input['show_profile'] = 0;
        }

        if ($request->show_profile_on_admin_website) {
            $input['show_profile_on_admin_website'] = 1;
        } else {
            $input['show_profile_on_admin_website'] = 0;
        }
        $data = Auth::user();
        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move(public_path('assets/front/img/user/'), $name);
            if ($data->photo != null) {
                @unlink(public_path('assets/front/img/user/' . $data->photo));
            }
            $input['photo'] = $name;
        }
        $data->update($input);
        Session::flash('success', __('Profile Updated Successfully') . '!');
        return "success";
    }

    public function resetform()
    {
        return view('user.reset');
    }

    public function reset(Request $request)
    {

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
            'confirmation_password' => 'required',
        ]);
        $user = Auth::user();
        if ($request->current_password) {
            if (Hash::check($request->current_password, $user->password)) {
                if ($request->new_password == $request->confirmation_password) {
                    $input['password'] = Hash::make($request->new_password);
                } else {
                    return back()->with('err', __('Confirm password does not match') . '.');
                }
            } else {
                return back()->with('err', __('Current password Does not match') . '.');
            }
        }

        $user->update($input);
        Session::flash('success', __('Password changed successfully') . '!');
        return back();
    }

    public function changePass()
    {
        return view('user.changepass');
    }

    public function updatePassword(Request $request)
    {
        $messages = [
            'password.required' => __('The new password field is required') . '.',
            'password.confirmed' => __("Password doesn't match") . '.'
        ];
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed'
        ], $messages);
        // if given old password matches with the password of this authenticated user...
        if (Hash::check($request->old_password, Auth::guard('web')->user()->password)) {
            $oldPassMatch = 'matched';
        } else {
            $oldPassMatch = 'not_matched';
        }
        if ($validator->fails() || $oldPassMatch == 'not_matched') {
            if ($oldPassMatch == 'not_matched') {
                $validator->errors()->add('oldPassMatch', true);
            }
            return redirect()->route('user.changePass')
                ->withErrors($validator);
        }

        // updating password in database...
        $user = App\Models\User::findOrFail(Auth::guard('web')->user()->id);
        $user->password = $request->password;
        $user->save();

        Session::flash('success', __('Password changed successfully') . '!');

        return redirect()->back();
    }

    public function shippingdetails()
    {
        $user = Auth::user();
        return view('user.shipping_details', compact('user'));
    }

    public function shippingupdate(Request $request)
    {
        $request->validate([
            "shpping_fname" => 'required',
            "shpping_lname" => 'required',
            "shpping_email" => 'required',
            "shpping_number" => 'required',
            "shpping_city" => 'required',
            "shpping_state" => 'required',
            "shpping_address" => 'required',
            "shpping_country" => 'required',
        ]);


        Auth::user()->update($request->all());

        Session::flash('success', __('Shipping Details Update Successfully') . '.');
        return back();
    }

    public function billingdetails()
    {
        $user = Auth::user();
        return view('user.billing_details', compact('user'));
    }

    public function billingupdate(Request $request)
    {
        $request->validate([
            "billing_fname" => 'required',
            "billing_lname" => 'required',
            "billing_email" => 'required',
            "billing_number" => 'required',
            "billing_city" => 'required',
            "billing_state" => 'required',
            "billing_address" => 'required',
            "billing_country" => 'required',
        ]);

        Auth::user()->update($request->all());

        Session::flash('success', __('Billing Details Update Successfully') . '.');
        return back();
    }

    public function changeTheme(Request $request)
    {
        return redirect()->back()->withCookie(cookie()->forever('user-theme', $request->theme));
    }


    public function changelanguage($id)
    {
        try {
            if (Session::has('tenant_dashboard_lang')) {
                Session::put('tenant_dashboard_lang', $id);
            }
            return 'success';
        } catch (\Exception $e) {
            return response()->$e->getMessage();
        }
    }
    public function secretLogin(Request $request)
    {

        $customer = Customer::where('id', $request->user_id)->first();

        $param = $customer->user->username;
        if ($customer) {
            Auth::guard('customer')->login($customer);;
            return redirect()->route('customer.dashboard', $param)
                ->withSuccess('You have Successfully loggedin');
        }

        return redirect()->route('customer.login', $param)->withSuccess(__('Oppes! You have entered invalid credentials') . '.');
    }
}
