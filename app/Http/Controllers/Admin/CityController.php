<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Traits\AdminLanguage;
use Illuminate\Validation\Rule;
use App\Http\Helpers\UploadFile;
use App\Models\User\Property\City;
use Illuminate\Support\Facades\DB;
use App\Models\User\Property\State;
use App\Http\Controllers\Controller;
use App\Models\User\Property\Country;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Models\User\Property\CityContent;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\PropertyManagement\CityStore;

class CityController extends Controller
{
    use AdminLanguage;
    public function index(Request $request)
    {
        $language = $this->currentLang();
        $name = request()->filled('name') ? request('name') : null;
        $tenantId = 0; // Admin context

        $information['Languages'] = $this->allLangs();

        $information['countries'] = Country::getCountries($tenantId, $language->id);

        $cities = City::getCities($tenantId, $language->id, $name);

        $information['states'] = State::getStates($tenantId, $language->id);
        $information['cities'] = collectionToPaginate($cities, 10);
        return view('admin.property-management.city.index', $information);
    }

    public function getCities(Request $request)
    {
        $userId = 0;
        $language = $this->currentLang();

        $cities = City::where([['state_id', $request->state_id], ['user_id', $userId]])->select('id')->orderBy('serial_number', 'ASC')->get();
        $cities->map(function ($city) use ($language) {
            $city->name = $city->getContent($language->id)->name;
        });
        return Response::json(['cities' => $cities], 200);
    }

    public function store(CityStore $request)
    {
        $tenantId = 0;
        $languages = $this->allLangs();

        $img = $request->file('image');
        $filename = null;
        if ($request->hasFile('image')) {
            $filename = UploadFile::store('assets/img/property-city/', $img);
        }
        DB::beginTransaction();

        try {
            if ($request->has('state') && $request->has('country')) {
                $state = State::where('user_id', $tenantId)->find($request->state) ?? null;
                $countryId =  $state->country->id ?? null;
                $stateId =  $state->id;
            } elseif ($request->has('country')) {
                $country = Country::find($request->country) ?? null;
                $countryId = $country?->id ?? null;
                $stateId =  null;
            } else {
                $countryId =  null;
                $stateId =  null;
            }

            $city  = new City();
            $city->user_id = $tenantId;
            $city->country_id = $countryId;
            $city->state_id =  $stateId;
            $city->image = $filename;
            $city->status = $request->status;
            $city->serial_number = $request->serial_number;
            $city->save();

            foreach ($languages as $lang) {

                CityContent::create([
                    'user_id' => $tenantId,
                    'name' => $request[$lang->code . '_name'],
                    'slug' => $request[$lang->code . '_name'],
                    'language_id' => $lang->id,
                    'city_id' => $city->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // Session::flash('warning', __($e->getMessage()));
            Session::flash('warning', __('Something went wrong!'));
            return 'success';
        }
        Session::flash('success', __('Added successfully!'));

        return 'success';
    }

    public function update(Request $request)
    {
        $tenantId = 0;
        $languages =  $this->allLangs();
        $rules = [

            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];
        if ($request->hasFile('image')) {
            $rules['image'] = "nullable|mimes:jpg,jpeg,svg,png,webp";
        }

        $message = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_city_contents', 'name')->ignore($request->id, 'city_id')->where('user_id', $tenantId)
                ];

            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' '  . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        DB::beginTransaction();
        try {
            $city = City::find($request->id);
            $filename = $city->image;
            if ($request->hasFile('image')) {
                $filename = UploadFile::update('assets/img/property-city/', $request->file('image'), $city->image);
            }

            $city->update([
                'image' => $filename,
                'status' => $request->status,
                'serial_number' => $request->serial_number
            ]);

            foreach ($languages as $lan) {
                $name = $request[$lan->code . '_name'] ?? null;

                if (!empty($name)) {
                    $content = CityContent::where([['city_id', $request->id], ['language_id', $lan->id]])->first();

                    if (empty($content)) {
                        $content  = new  CityContent();
                        $content->user_id = $tenantId;
                        $content->city_id = $request->id;
                        $content->language_id = $lan->id;
                        $content->save();
                    }
                    $content->update([
                        'name' => $name,
                        'slug' => $name,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', __('Something went wrong!'));
            return   'success';
        }

        Session::flash('success', __('Updated successfully!'));

        return  'success';
    }

    public function updateFeatured(Request $request)
    {
        $city = City::findOrFail($request->cityId);

        if ($request->featured == 1) {
            $city->update(['featured' => 1]);
        } else {
            $city->update(['featured' => 0]);
        }
        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }



    public function destroy(Request $request)
    {

        $tenantId = 0;
        $city = City::where([['user_id', $tenantId], ['id', $request->id]])->firstOrFail();
        $delete = $city->deleteCity();


        if ($delete) {
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('You can not delete city! A property included in this city.'));
        }
        return redirect()->back();
    }


    public function bulkDestroy(Request $request)
    {


        $ids = $request->ids;
        $tenantId = 0;

        foreach ($ids as $id) {;

            $city = City::where([['user_id', $tenantId], ['id',  $id]])->firstOrFail();
            $delete = $city->deleteCity();

            if ($delete == false) {

                Session::flash('warning', __('You can not delete all city!  The property included the city.'));

                return  'success';
            }
        }

        Session::flash('success', __('Deleted successfully!'));

        return  'success';
    }
}
