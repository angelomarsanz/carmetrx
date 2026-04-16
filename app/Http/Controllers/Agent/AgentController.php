<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\BasicSetting;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting as UserBasicSetting;
use App\Models\User\Project\Contact;
use App\Traits\Tenant\TenantLanguage;
use App\Models\User\Project\Project;
use App\Models\User\Property\Property;
use App\Models\User\Property\PropertyContact;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    use TenantLanguage;
    public function change_language(Request $request)
    {

        $adminLang = $this->selectLang($request->language);
        if ($adminLang) {
            session()->put('agent_dashboard_lang', $adminLang->code);
        } else {
            $adminLang = $this->defaultLang();
            session()->put('agent_dashboard_lang', $adminLang->code);
        }
        return redirect()->back();
    }

    public function dashboard()
    {

        $agent_id = Auth::guard('agent')->user()->id;
        $information['totalProperties'] = Property::query()->where('agent_id', $agent_id)->count();
        $information['totalProjects'] = Project::query()->where('agent_id', $agent_id)->count();

        $totalProperties = DB::table('user_properties')
            ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
            ->groupBy('month')
            ->where('agent_id', $agent_id)
            ->whereYear('created_at', '=', date('Y'))
            ->get();

        $totalProjects = DB::table('user_projects')
            ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
            ->groupBy('month')
            ->where('agent_id', $agent_id)
            ->whereYear('created_at', '=', date('Y'))
            ->get();

        $information['propertyMessages'] = PropertyContact::where('agent_id', $agent_id)->count();
        $information['projectMessages'] = Contact::where('agent_id', $agent_id)->count();


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


        $information['monthArr'] = $months;
        $information['totalPropertiesArr'] = $totalPropertyArr;
        $information['totalProjectsArr'] = $totalProjectsArr;
        return view('agent.index', $information);
    }

    //login
    public function login()
    {
      
        $queryResult['bs'] = BasicSetting::query()->select('google_recaptcha_status', 'facebook_login_status', 'google_login_status')->first();
        return view('tenant_frontend.agent.auth.login', $queryResult);
    }
    public function logout(Request $request)
    {
        Auth::guard('agent')->logout();
        Session::forget('agent_theme_version');
        return redirect()->route('frontend.agent.login', getParam());
    }
    //authenticate
    public function authentication(Request $request)
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        $tenantId = getUser()->id;
        $info = UserBasicSetting::where('user_id', $tenantId)->select('google_recaptcha_status')->first();
        if ($info->google_recaptcha_status == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $messages = [];

        if ($info->google_recaptcha_status == 1) {
            $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
            $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $agent = Agent::where('username', $request->username)->where('user_id', $tenantId)->first();
        if ($agent && $agent->status == 0) {
            return redirect()->back()->with('error', __('Your account is deactivate'));
        } elseif ($agent && Hash::check($request->password, $agent->password)) {

            // Log in the correct agent
            Auth::guard('agent')->login($agent);

            // Store session value
            Session::put('secret_login', 0);

            return redirect()->route('agent.dashboard', getParam());
        }

        return redirect()->back()->with('error', 'Incorrect username or password');
    }

    public function changeTheme(Request $request)
    {
        Session::put('agent_theme_version', $request->agent_theme_version);
        return redirect()->back();
    }
}
