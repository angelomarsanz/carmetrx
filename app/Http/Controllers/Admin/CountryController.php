<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Traits\AdminLanguage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Property\Country;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User\Property\CountryContent;

class CountryController extends Controller
{
    use AdminLanguage;
    public function index(Request $request)
    {
        $language = $this->currentLang();
        $name = request()->filled('name') ? request('name') : null;
        $information['Languages'] = $this->allLangs();

        $countries = Country::getCountries(0, $language->id, $name);

        $information['countries'] = collectionToPaginate($countries, 10);
        return view('admin.property-management.country.index', $information);
    }

    public function store(Request $request)
    {
        $languages = $this->allLangs();
        $message = [];
        $rules = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_country_contents', 'name')->where('user_id', 0)

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
                'user_id' => 0
            ]);
            foreach ($languages as $lan) {
                CountryContent::create([
                    'name' => $request[$lan->code . '_name'],
                    'user_id' => 0,
                    'country_id' => $country->id,
                    'language_id' => $lan->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', 'Something went wrong!');
            return  'success';
        }
        Session::flash('success', __('Added successfully!'));
        return  'success';
    }

    public function update(Request $request)
    {
        $languages = $this->allLangs();

        $message = [];
        $rules = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_country_contents', 'name')
                        ->ignore($request->id, 'country_id')->where('user_id', 0)
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
                $countryContent = CountryContent::where([['country_id', $request->id], ['language_id', $lan->id], ['user_id', 0]])->first();
                if (empty($countryContent)) {
                    $countryContent  = new  CountryContent();
                    $countryContent->user_id = 0;
                    $countryContent->country_id = $request->id;
                    $countryContent->name =  $name;
                    $countryContent->language_id = $lan->id;
                    $countryContent->save();
                } else {
                    $countryContent->name =  $name;
                    $countryContent->language_id = $lan->id;
                    $countryContent->save();
                }
            }
        }

        Session::flash('success', __('Updated successfully!'));
        return 'success';
    }


    public function destroy(Request $request)
    {
        $country = Country::where([['user_id', 0], ['id', $request->id]])->firstOrFail();
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

        foreach ($ids as $id) {;

            $country = Country::where([['user_id', 0], ['id',  $id]])->firstOrFail();
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
