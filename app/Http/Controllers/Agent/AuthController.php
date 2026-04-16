<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User\Agent\Agent;
use App\Http\Helpers\UploadFile;
use Illuminate\Support\Facades\DB;
use App\Rules\MatchOldPasswordRule;
use App\Models\User\Agent\AgentInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use TenantFrontendLanguage;

    public function edit_profile()
    {
        $information = [];
        $agent = Auth::guard('agent')->user();
      
        $information['tenantLangs'] = $this->allLangs($agent->user_id);

        $information['agent'] = Agent::with('agentInfo')->where('id', $agent->id)->first();
        return view('agent.auth.edit-profile', $information);
    }
    //update_profile
    public function update_profile(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $tenanId = $agent->user_id;
        $rules = [
            'username' => [
                'required',
                'max:15',
                Rule::unique('user_agents', 'username')->where(function ($query) use ($tenanId) {
                    return $query->where('user_id', $tenanId);
                })->ignore($agent->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('user_agents', 'email')->where(function ($query) use ($tenanId) {
                    return $query->where('user_id', $tenanId);
                })->ignore($agent->id)
            ],
            'phone' => 'required|numeric'
        ];

        if ($request->hasFile('photo')) {
            $rules['photo'] = 'mimes:png,jpeg,jpg,svg';
        }

        $languages = $this->allLangs($agent->user_id);
        foreach ($languages as $language) {
            $rules[$language->code . '_first_name'] = 'required';
            $rules[$language->code . '_last_name'] = 'required';
        }

        $messages = [];

        foreach ($languages as $language) {
            $messages[$language->code . '_first_name.required'] = __('The First Name field is required for') . $language->name . ' ' . __('language');
            $messages[$language->code . '_last_name.required'] = __('The Last Name field is required for') . $language->names . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }
        $in = $request->all();
        $file = $request->file('photo');
        if ($file) {
            $fileName = UploadFile::update('assets/img/agents/', $request->photo, $agent->image);
            $in['image'] = $fileName;
        }

        if ($request->show_email_addresss) {
            $in['show_email_addresss'] = 1;
        } else {
            $in['show_email_addresss'] = 0;
        }
        if ($request->show_phone_number) {
            $in['show_phone_number'] = 1;
        } else {
            $in['show_phone_number'] = 0;
        }
        if ($request->show_contact_form) {
            $in['show_contact_form'] = 1;
        } else {
            $in['show_contact_form'] = 0;
        }
        DB::beginTransaction();
        try {
            $agent = Agent::find($agent->id);

            $agent->update([
                'email' => $request->email,
                'phone' => $request->phone,
                'show_email_addresss' => $request->show_email_addresss ? 1 : 0,
                'show_phone_number' => $request->show_phone_number ? 1 : 0,
                'show_contact_form' => $request->show_contact_form ? 1 : 0
            ]);

            $agent_id = $agent->id;
            foreach ($languages as $language) {
                $agentInfo = AgentInfo::where('agent_id', $agent_id)->where('language_id', $language->id)->first();
                if ($agentInfo == NULL) {
                    $agentInfo = new AgentInfo();
                }
                $agentInfo->language_id = $language->id;
                $agentInfo->agent_id = $agent_id;
                $agentInfo->first_name = $request[$language->code . '_first_name'];
                $agentInfo->last_name = $request[$language->code . '_last_name'];
                $agentInfo->country = $request[$language->code . '_country'];
                $agentInfo->city = $request[$language->code . '_city'];
                $agentInfo->state = $request[$language->code . '_state'];
                $agentInfo->zip_code = $request[$language->code . '_zip_code'];
                $agentInfo->address = $request[$language->code . '_address'];
                $agentInfo->details = $request[$language->code . '_details'];
                $agentInfo->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return Response::json(['status' => 'errors', $e->getMessage()], 402);
        }

        Session::flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    //change_password
    public function change_password()
    {
        return view('agent.auth.change-password');
    }

    //update_password
    public function updated_password(Request $request)
    {
        $rules = [
            'current_password' => [
                'required',
                new MatchOldPasswordRule('agent')

            ],
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ];

        $messages = [
            'new_password.confirmed' => __('Password confirmation does not match'),
            'new_password_confirmation.required' => __('The confirm new password field is required')
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $agent = Agent::findOrFail(Auth::guard('agent')->user()->id);

        $agent->update([
            'password' => $request->new_password
        ]);

        Session::flash('success', __('Updated successfully!'));

        return 'success';
    }
}
