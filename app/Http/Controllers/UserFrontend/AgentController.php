<?php

namespace App\Http\Controllers\UserFrontend;

use App\Jobs\MailToAgent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Rules\MatchEmailRule;
use App\Jobs\PasswordResetMail;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Models\User\MailTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User\Agent\AgentInfo;
use App\Models\User\Project\Project;
use Illuminate\Support\Facades\Hash;
use App\Models\User\Property\Category;
use App\Models\User\Property\Property;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;

class AgentController extends Controller
{
    use TenantFrontendLanguage, PageHeadings;
    public function index($username, Request $request)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();
        $language = $this->currentLang($tenantId);

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_agents', 'meta_description_agents')->first();

        $queryResult['pageHeading'] = $this->pageHeading($tenantId);

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        $queryResult['agents'] = Agent::search($request, $tenantId, $language->id)->paginate(9);
        return view('tenant_frontend.agent.index', $queryResult);
    }

    public function details($username, Request $request, $slug)
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);

        $adminLang = getPropertyspectLang($language);
        $queryResult['adminLang'] = $adminLang;


        $agent = Agent::where([['username', $slug], ['user_id', $tenantId], ['status', 1]])->firstOrFail();

        $agentInfo = AgentInfo::where([['agent_id', $agent->id], ['language_id', $language->id]])->first();
        $queryResult['agentInfo'] = $agentInfo;
        $agent_id = $agent->id;

        $queryResult['agent'] = $agent;

        $queryResult['all_properties'] = Property::where([['user_properties.agent_id', $agent_id], ['user_properties.status', 1]])
            ->where('user_property_contents.language_id', $language->id)
            ->join('user_property_contents', 'user_property_contents.property_id', 'user_properties.id')
            ->select('user_properties.*', 'user_property_contents.language_id', 'user_property_contents.slug', 'user_property_contents.title')
            ->orderBy('user_properties.id', 'desc')
            ->get();
        $queryResult['all_projects'] = Project::where('user_projects.agent_id', $agent_id)
            ->join('user_project_contents', 'user_project_contents.project_id', 'user_projects.id')
            ->where('user_project_contents.language_id', $language->id)
            ->select('user_projects.*', 'user_project_contents.language_id', 'user_project_contents.title', 'user_project_contents.slug', 'user_project_contents.address', 'user_project_contents.description')
            ->orderBy('id', 'desc')
            ->get();

        $uniqueCategoryIds = $queryResult['all_properties']->pluck('categoryContent.category_id')->unique();


        $queryResult['categories'] = Category::with(['categoryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->where('status', 1)->whereIn('id', $uniqueCategoryIds)->get();


        $queryResult['currencyInfo'] = $this->getUserCurrencyInfo($tenantId);
        return view('tenant_frontend.agent.details', $queryResult);
    }

    public function tenantDetails($username, Request $request)
    {
        $tenant = getUser();
        $tenantId = $tenant->id;

        $language = $this->currentLang($tenantId);
        $queryResult['tenant'] = $tenant;

        $queryResult['all_properties'] = Property::where([['user_properties.user_id', $tenantId], ['user_properties.status', 1]])
            ->where('user_property_contents.language_id', $language->id)
            ->join('user_property_contents', 'user_property_contents.property_id', 'user_properties.id')
            ->select('user_properties.*', 'user_property_contents.language_id', 'user_property_contents.slug', 'user_property_contents.title')
            ->orderBy('user_properties.id', 'desc')
            ->get();
        $queryResult['all_projects'] = Project::where('user_projects.user_id', $tenantId)
            ->join('user_project_contents', 'user_project_contents.project_id', 'user_projects.id')
            ->where('user_project_contents.language_id', $language->id)
            ->select('user_projects.*', 'user_project_contents.language_id', 'user_project_contents.title', 'user_project_contents.slug', 'user_project_contents.address', 'user_project_contents.description')
            ->orderBy('id', 'desc')
            ->get();

        $uniqueCategoryIds = $queryResult['all_properties']->pluck('categoryContent.category_id')->unique();


        $queryResult['categories'] = Category::with(['categoryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->where('status', 1)->whereIn('id', $uniqueCategoryIds)->get();


        $queryResult['currencyInfo'] = $this->getUserCurrencyInfo($tenantId);
        return view('tenant_frontend.agent.tenant-details', $queryResult);
    }

    public function login()
    {
        $tenantId = getUser()->id;
        $queryResult['bs'] = BasicSetting::where('user_id', $tenantId)->select('google_recaptcha_status', 'google_login_status')->first();
        return view('tenant_frontend.agent.auth.login', $queryResult);
    }

    //forget_passord
    public function forget_passord()
    {
        $tenantId = getUser()->id;

        $queryResult['bs'] = BasicSetting::where('user_id', $tenantId)->select('google_recaptcha_status', 'facebook_login_status', 'google_login_status')->first();
        return view('tenant_frontend.agent.auth.forget-password', $queryResult);
    }

    //forget_mail
    public function forget_mail(Request $request)
    {
        $rules = [
            'email' => [
                'required',
                'email:rfc,dns',
                new MatchEmailRule('agent')
            ]
        ];
        $tenant = getUser();
        $tenantId = $tenant->id;
        $info = BasicSetting::where('user_id', $tenantId)->select('google_recaptcha_status')->first();
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $agent = Agent::where('email', $request->email)->first();
        $token =  Str::random(32);
        $route = route('frontend.agent.reset.password', [getParam(), 'token' => $token]);
        DB::table('password_resets')->insert([
            'email' => $agent->email,
            'token' => $token,
        ]);
        $routeLink  = "<a href='{$route}'> Click Here</a>";
        PasswordResetMail::dispatchSync($agent, $routeLink);

        return redirect(route('frontend.agent.forget.password', [getParam()]));
    }
    //reset_password
    public function reset_password()
    {
        return view('tenant_frontend.agent.auth.reset-password');
    }
    //update_password
    public function update_password(Request $request)
    {
        $rules = [
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ];

        $messages = [
            'new_password.confirmed' => 'Password confirmation failed.',
            'new_password_confirmation.required' => 'The confirm new password field is required.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $reset = DB::table('password_resets')->where('token', $request->token)->first();

        if (!$reset) {
            Session::flash('error', 'Something went wrong!');
            return redirect()->back();
        }
        $email = $reset->email;
        $agent = Agent::where('email',  $email)->first();
        $param = $agent->user->username;

        $agent->update([
            'password' => Hash::make($request->new_password)
        ]);
        DB::table('password_resets')->where('token', $request->token)->delete();
        Session::flash('success', 'Reset Your Password Successfully Completed.Please Login Now');


        return redirect()->route('frontend.agent.login', $param);
    }

    public function contactAgent(Request $request)
    {

        $rules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns',
            'phone' => 'required|numeric',
            'message' => 'required'
        ];
        $userId = getUser()->id;
        $info = BasicSetting::where('user_id', $userId)->select('google_recaptcha_status')->first();
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
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        try {
            $agent = Agent::find($request->agent_id);
            if ($agent) {
                MailToAgent::dispatch($agent, $request->all());
            }
        } catch (\Exception $e) {
            return back()->with('error', __('Something went wrong!'));
        }



        return back()->with('success', __('Message sent successfully'));
    }
}
