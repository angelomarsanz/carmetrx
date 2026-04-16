<?php

namespace App\Http\Controllers\User;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\Agent\RegisterRequest;
use App\Http\Requests\Agent\UpdateRequest;
use App\Jobs\RegisteredAgentMail;
use App\Models\User\Agent\Agent;
use App\Models\User\Agent\AgentInfo;
use App\Models\User\BasicSetting;
use App\Models\User\Language;
use App\Models\User\MailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;

class AgentController extends Controller
{
    use TenantFrontendLanguage;
    public function index()
    {
        $agents = Agent::where('user_id', Auth::guard('web')->user()->id)->paginate(10);

        return view('user.agent.index', compact('agents'));
    }

    public function store(RegisterRequest $request)
    {
        $tenant = Auth::guard('web')->user();
        $tenantId =  $tenant->id;
        DB::beginTransaction();
        $requestData = $request->all();
        if ($request->has('image')) {
            $requestData['imageName'] = UploadFile::store(Constant::AGENT_IMAGE . '/', $request->file('image'));
        } else {
            $requestData['imageName'] = null;
        }

        try {
            $requestData['status'] = 1;
            $agent =  Agent::storeAgent($tenantId, $requestData);


            $languages = $this->allLangs($tenantId);

            foreach ($languages as $lang) {
                AgentInfo::sotreInfo($agent->id, $lang->id, $requestData);
            }
            $mailData = [
                'login_url' =>   '<a href=' . route("frontend.agent.login", $tenant->username) . '> Click Here  </a>',
                'password' => $request->password,
            ];
            RegisteredAgentMail::dispatch($agent, $mailData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', __('Something went wrong!'));
            return response()->json(['status' => $e->getMessage()], 200);
        }

        Session::flash('success', __('Added successfully!'));
        return response()->json('success');
    }
    public function update(UpdateRequest $request)
    {
        $agent = Agent::where('user_id', Auth::guard('web')->user()->id)->find($request->id);

        if ($request->hasFile('image')) {
            $imageName = UploadFile::update(Constant::AGENT_IMAGE . '/', $request->file('image'), $agent->image);
        }
        $agent->update($request->except('image'));
        if ($request->hasFile('image')) {
            $agent->update([
                'image' =>  $imageName
            ]);
        }

        Session::flash('success', __('Updated successfully!'));

        return response()->json('success');
    }
    public function changeStatus(Request $request, $id)
    {
        $agent = Agent::where('user_id', Auth::guard('web')->user()->id)->findOrFail($id);
        $agent->status = $request->status;
        $agent->save();
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    //secrtet login
    public function secret_login($id)
    {
        $agent = Agent::where('id', $id)->first();
        $param = $agent->user->username;
        if ($agent) {
            Auth::guard('agent')->login($agent);
            return redirect()->route('agent.dashboard', $param)
                ->withSuccess(__('You have Successfully loggedin'));
        }
    }

    public function destroy($id)
    {
        $agent = Agent::where('id', $id)->first();
        if ($agent) {
            $agent->destroyAgent();

            Session::flash('success', __('Deleted successfully!'));
            return redirect()->back();
        }
    }
  
}
