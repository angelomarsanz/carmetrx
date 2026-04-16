<?php

namespace App\Http\Controllers\User\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\User\BasicSetting;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Models\User\Project\City;
use App\Models\User\Project\Country;
use App\Models\User\Project\State;
use App\Models\User\Project\StateContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }
        $name = request()->filled('name') ? request('name') : null;
        $information['tenantFrontLangs'] = $this->allLangs($tenantId);

        $states = State::getStates($tenantId, $language->id, $name);

        $information['countries'] = Country::getCountries($tenantId, $language->id);
        $information['states'] = collectionToPaginate($states, 10);
        return view('user.project-management.state.index', $information);
    }
    public function getState(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $language = $this->currentLang($tenantId);
        $states = State::where([['user_id', $tenantId], ['country_id', $request->id]])->get()->map(function ($state) use ($language) {
            $state->name = $state->getContent($language->id)?->name;
            return $state;
        });
        return Response::json($states, 200);
    }

    public function getStateCities(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->currentLang($userId);

        $states = State::where('country_id', $request->id)->select('id')->get();
        $states->map(function ($state) use ($language) {
            $state->name = $state->getContent($language->id)->name;
        });

        $cities = City::where('country_id', $request->id)->where('status', 1)->select('id')->orderBy('serial_number', 'ASC')->get();
        $cities->map(function ($city) use ($language) {
            $city->name = $city->getContent($language->id)->name;
        });
        return Response::json(['states' => $states, 'cities' => $cities], 200);
    }
    public function store(Request $request)
    {

        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        $rules = [];
        $message = [];

        $basicSettings = BasicSetting::where('user_id', $tenantId)->select('project_state_status', 'project_country_status')->first();
        if ($basicSettings->project_country_status == 1) {
            $rules['country'] = 'required';
        }

        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_project_state_contents', 'name')->where('user_id', $tenantId)
                ];
            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' '  . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }
        DB::beginTransaction();
        try {
            if ($basicSettings->project_country_status == 1) {
                $country = Country::where('user_id', $tenantId)->find($request->country);
                $countryId = $country->id;
            } else {
                $countryId = null;
            }

            $state = State::create([
                'country_id' => $countryId,
                'user_id' => $tenantId,
            ]);
            foreach ($languages as $lang) {
                StateContent::create([
                    'user_id' => $tenantId,
                    'name' => $request[$lang->code . '_name'],
                    'slug' => make_slug($request[$lang->code . '_name']),
                    'language_id' => $lang->id,
                    'state_id' => $state->id,

                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', $e->getMessage());
            return   'success';
        }
        Session::flash('success', __('Added successfully!'));
        return  'success';
    }

    public function update(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        $rules = [];
        $message = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_state_contents', 'name')->ignore($request->id, 'state_id')->where('user_id', $tenantId)
                ];
            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' '  . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        foreach ($languages as $lan) {
            $name = $request[$lan->code . '_name'] ?? null;

            if (!empty($name)) {
                $state = StateContent::where([['state_id', $request->id], ['language_id', $lan->id]])->first();
                if (empty($state)) {
                    $state  = new  StateContent();
                    $state->user_id = $tenantId;
                    $state->state_id = $request->id;
                    $state->language_id = $lan->id;
                    $state->save();
                }
                $state->update([
                    'name' => $name,
                    'slug' => make_slug($name)
                ]);
            }
        }

        Session::flash('success', __('Updated successfully!'));
        return  'success';
    }

    public function destroy(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $state = State::where([['user_id', $tenantId], ['id', $request->id]])->firstOrFail();
        $delete = $state->deleteState();


        if ($delete) {
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('You can not delete state! A property or city included in this state.'));
        }
        return redirect()->back();
    }

    public function bulkDestroy(Request $request)
    {

        $ids = $request->ids;
        $tenantId = Auth::guard('web')->user()->id;

        foreach ($ids as $id) {;

            $state = State::where([['user_id', $tenantId], ['id',  $id]])->firstOrFail();
            $delete = $state->deleteState();

            if ($delete == false) {

                Session::flash('warning', __('You can not delete all  state! A property,state or city included in this state.'));

                return  'success';
            }
        }

        Session::flash('success', __('Deleted successfully!'));

        return  'success';
    }
}
