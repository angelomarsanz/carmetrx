<?php

namespace App\Http\Controllers\User\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\User\Project\Country;
use App\Models\User\Project\CountryContent;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CountryController extends Controller
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

        $countries = Country::getCountries($tenantId, $language->id, $name);
        $information['countries'] = collectionToPaginate($countries, 10);
        return view('user.project-management.country.index', $information);
    }

    public function store(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        $message = [];
        $rules = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_project_country_contents', 'name')->where('user_id', $tenantId)

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

            $country =  Country::create([
                'user_id' => $tenantId
            ]);
            foreach ($languages as $lan) {
                CountryContent::create([
                    'name' => $request[$lan->code . '_name'],
                    'user_id' => $tenantId,
                    'country_id' => $country->id,
                    'language_id' => $lan->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', __('Something went wrong!'));
            return  'success';
        }
        Session::flash('success', __('Added successfully!'));
        return  'success';
    }

    public function update(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        $message = [];
        $rules = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_project_country_contents', 'name')->ignore($request->id, 'country_id')->where('user_id', $tenantId)
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
        try {

            foreach ($languages as $lan) {

                $name = $request[$lan->code . '_name'] ?? null;

                if (!empty($name)) {
                    $countryContent = CountryContent::where([['country_id', $request->id], ['language_id', $lan->id], ['user_id', $tenantId]])->first();
                    if (empty($countryContent)) {
                        $countryContent  = new  CountryContent();
                        $countryContent->user_id = $tenantId;
                        $countryContent->country_id = $request->id;
                        $countryContent->name =  $name;
                        $countryContent->language_id = $lan->id;
                        $countryContent->save();
                    } else {
                        $countryContent->name =  $name;
                        $countryContent->save();
                    }
                }
            }
            Session::flash('success', __('Updated successfully!'));
        } catch (Exception $e) {
            Session::flash('warning', __('Something went wrong!'));
        }


        return 'success';
    }


    public function destroy(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $country = Country::where([['user_id', $tenantId], ['id', $request->id]])->firstOrFail();
        $delete = $country->deleteCountry();


        if ($delete) {
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('You can not delete Country!! A property, state or city included in this country.'));
        }
        return redirect()->back();
    }

    public function bulkDestroy(Request $request)
    {

        $ids = $request->ids;
        $tenantId = Auth::guard('web')->user()->id;

        foreach ($ids as $id) {;

            $country = Country::where([['user_id', $tenantId], ['id',  $id]])->firstOrFail();
            $delete = $country->deleteCountry();

            if ($delete == false) {

                Session::flash('warning', __('You can not delete all  country! A property,state or city included in this country.'));

                return  'success';
            }
        }

        Session::flash('success', __('Deleted successfully!'));

        return  'success';
    }
}
