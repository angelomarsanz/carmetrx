<?php

namespace App\Http\Controllers\User\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\ProjectManagment\CityStore;
use App\Models\User\Project\City;
use App\Models\User\Project\CityContent;
use App\Models\User\Project\Country;
use App\Models\User\Project\State;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CityController extends Controller
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

        $information['countries'] = Country::getCountries($tenantId, $language->id);

        $cities = City::getCities($tenantId, $language->id, $name);

        $information['states'] = State::getStates($tenantId, $language->id);

        $information['cities'] = collectionToPaginate($cities, 10);
        return view('user.project-management.city.index', $information);
    }

    public function getCities(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->currentLang($userId);

        $cities = City::where([['state_id', $request->state_id], ['user_id', $userId]])->select('id')->orderBy('serial_number', 'ASC')->get();
        $cities->map(function ($city) use ($language) {
            $city->name = $city->getContent($language->id)->name;
        });
        return Response::json(['cities' => $cities], 200);
    }

    public function store(CityStore $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        $img = $request->file('image');
        $filename = null;
        if ($request->hasFile('image')) {
            $filename = UploadFile::store('assets/img/project-city/', $img);
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
            Session::flash('warning', __('Something went wrong!'));
            return 'success';
        }
        Session::flash('success', __('Added successfully!'));

        return 'success';
    }

    public function update(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages =  $this->allLangs($tenantId);
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
                    Rule::unique('user_project_city_contents', 'name')->ignore($request->id, 'city_id')->where('user_id', $tenantId)
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
                $filename = UploadFile::update('assets/img/project-city/', $request->file('image'), $city->image);
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

        $tenantId = Auth::guard('web')->user()->id;
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
        $tenantId = Auth::guard('web')->user()->id;

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
