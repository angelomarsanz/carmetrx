<?php

namespace App\Http\Controllers\User\PropertyManagement;

use Illuminate\Http\Request;
use App\Http\Helpers\UploadFile;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Models\User\Property\City;
use Illuminate\Support\Facades\DB;
use App\Models\User\Property\State;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Property\Amenity;
use App\Models\User\Property\Country;
use App\Models\User\Property\Category;
use App\Models\User\Property\Property;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Models\User\Property\SliderImage;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\Property\PropertyAmenity;
use App\Models\User\Property\PropertyContent;
use App\Models\User\Property\PropertySpecification;
use App\Models\User\Property\PropertySpecificationContent;
use App\Http\Requests\PropertyManagement\PropertyStoreRequest;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Http\Requests\PropertyManagement\PropertyUpdateRequest;

class PropertyController extends Controller
{
    use TenantFrontendLanguage;

    public function type()
    {
        $tenantId = Auth::guard('web')->user()->id;
        $data['commertialCount'] = Property::where('user_id', $tenantId)->where('type', 'commercial')->count();
        $data['residentialCount'] = Property::where('user_id', $tenantId)->where('type', 'residential')->count();
        return view('user.property-management.type', $data);
    }

    public function settings()
    {
        $content = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('property_country_status', 'property_state_status')->first();
        return view('user.property-management.settings', compact('content'));
    }
    //update_setting
    public function update_settings(Request $request)
    {
        $request->validate([
            'property_country_status' => 'required',
            'property_state_status' => 'required',
        ]);
        $status = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->first();
        $status->property_country_status = $request->property_country_status;
        $status->property_state_status = $request->property_state_status;
        $status->save();
        Session::flash('success', __('Updated successfully!'));
        return back();
    }
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {

            $language = $this->defaultLang($tenantId);
        }

        $language_id = $language->id;
        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }

        $data['properties'] = Property::where('user_properties.user_id', $tenantId)
            ->leftJoin('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            // ->leftJoin('user_city_contents', function ($join) use ($language_id) {
            //     $join->on('user_properties.city_id', '=', 'user_city_contents.city_id')
            //         ->where('user_city_contents.language_id', '=', $language_id);
            // })
            ->when($title, function ($query) use ($title) {
                return $query->where('user_property_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->where('user_property_contents.language_id', $language_id)
            ->select(
                'user_properties.id',
                'user_properties.type',
                'user_properties.agent_id',
                'user_properties.status',
                'user_properties.city_id',
                'user_properties.featured',
                // 'user_city_contents.name as city_name',
                'user_property_contents.title'
            )
            ->orderBy('user_properties.id', 'desc')
            ->paginate(10);

        return view('user.property-management.index', $data);
    }

    public function create(Request $request)
    {
        $information = [];
        $tenantId  = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        $information['language'] = $this->defaultLang($tenantId);
        $information['languages'] = $languages;
        $information['agents'] = Agent::where([['user_id', $tenantId], ['status', 1]])->get();

        $information['propertyCategories'] = Category::where('user_id', 0)
            ->where([['type', $request->type], ['status', 1]])
            ->get();
        $information['propertyCountries'] = Country::where('user_id', 0)->get();
        $information['amenities'] = Amenity::where('user_id', 0)->where('status', 1)->get();
        $information['states'] = State::where('user_id', 0)->get();
        $information['cities'] = City::where('user_id', 0)->where('status', 1)->get();

        $information['propertySettings'] = BasicSetting::where('user_id', $tenantId)
            ->select('property_state_status', 'property_country_status')->first();

        return view('user.property-management.create', $information);
    }

    public function updateFeatured(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $currentPackage =  UserPermissionHelper::currentPackage($tenantId);
        $property = Property::where('user_id', $tenantId)->findOrFail($request->requestId);
        $featuredCount = Property::where([['user_id', $tenantId], ['featured', 1]])->count();

        if ($request->featured == 1) {
            if ($currentPackage->number_of_property_featured  > $featuredCount) {
                $property->update(['featured' => 1]);
                Session::flash('success', __('Updated successfully!'));
            } else {
                Session::flash('downgrade', __('Listing limit reached or exceeded!'));
            }
        } else {
            $property->update(['featured' => 0]);
            Session::flash('success', __('Updated successfully!'));
        }

        return redirect()->back();
    }

    public function imagesstore(Request $request)
    {
        $img = $request->file('file');
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg', 'webp');
        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    $ext = $img->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg images are allowed");
                    }
                },
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $imageName = UploadFile::store('assets/img/property/slider-images/', $request->file('file'));

        $pi = new SliderImage();
        if (!empty($request->property_id)) {
            $pi->property_id = $request->property_id;
        }
        $pi->user_id = Auth::guard('web')->user()->id;
        $pi->image = $imageName;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }
    public function imagermv(Request $request)
    {
        $pi = SliderImage::findOrFail($request->fileid);
        $imageCount = SliderImage::where('property_id', $pi->property_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/property/slider-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }

    //imagedbrmv
    public function imagedbrmv(Request $request)
    {
        $pi = SliderImage::findOrFail($request->fileid);
        $imageCount = SliderImage::where('property_id', $pi->property_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/property/slider-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }
    public function videoImgrmv(Request $request)
    {
        $pi = Property::select('video_image', 'id')->findOrFail($request->fileid);

        if (!empty($pi->video_image)) {
            @unlink(public_path('assets/img/property/video/') . $pi->video_image);
            $pi->video_image = null;
            $pi->save();
            return 'success';
        } else {
            return 'false';
        }
    }

    public function floorImgrmv(Request $request)
    {
        $pi = Property::select('floor_planning_image', 'id')->findOrFail($request->fileid);

        if (!empty($pi->floor_planning_image)) {
            @unlink(public_path('assets/img/property/plannings/') . $pi->floor_planning_image);
            $pi->floor_planning_image = null;
            $pi->save();
            return 'success';
        } else {
            return 'false';
        }
    }
    public function store(PropertyStoreRequest $request)
    {

        DB::transaction(function () use ($request) {
            $tenant = Auth::guard('web')->user();
            $featuredImgURL = $request->featured_image;
            if (request()->hasFile('featured_image')) {
                $featuredImgName = UploadFile::store('assets/img/property/featureds', $featuredImgURL);
            }

            $floorPlanningImage = null;
            $videoImage = null;
            if (request()->hasFile('floor_planning_image')) {
                $floorPlanningImage = UploadFile::store('assets/img/property/plannings', $request->floor_planning_image);
            }

            if ($request->hasFile('video_image')) {
                $videoImage = UploadFile::store('assets/img/property/video/', $request->video_image);
            }
            $requestData = $request->all();
            $requestData['featuredImgName'] = $featuredImgName;
            $requestData['floorPlanningImage'] = $floorPlanningImage;
            $requestData['videoImage'] = $videoImage;
            $property = Property::storeProperty($tenant->id, $requestData);

            $slders = $request->slider_images;
            if ($slders) {
                $pis = SliderImage::findOrFail($slders);
                foreach ($pis as $key => $pi) {
                    $pi->property_id = $property->id;
                    $pi->save();
                }
            }

            if ($request->has('amenities')) {
                foreach ($request->amenities as $amenity) {
                    PropertyAmenity::create([
                        'property_id' => $property->id,
                        'amenity_id' => $amenity,
                        'user_id' => $tenant->id
                    ]);
                }
            }

            $languages = $this->allLangs($tenant->id);

            foreach ($languages as $language) {

                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                    $contentData  = [
                        'language_id' => $language->id,
                        'title' => $request[$language->code . '_title'],
                        'slug' => $request[$language->code . '_title'],
                        'summary' => $request[$language->code . '_summary'],
                        'address' => $request[$language->code . '_address'],
                        'description' => $request[$language->code . '_description'],
                        'meta_keyword' => $request[$language->code . '_meta_keyword'],
                        'meta_description' => $request[$language->code . '_meta_description'],
                    ];
                    PropertyContent::storePropertyContent($property->id, $contentData);
                }

                $label_datas = $request[$language->code . '_label'];
                foreach ($label_datas as $key => $data) {
                    if (!empty($request[$language->code . '_value'][$key])) {
                        $property_specification = PropertySpecification::where([['property_id', $property->id], ['key', $key]])->first();
                        if (is_null($property_specification)) {
                            $property_specification = PropertySpecification::storeSpecification($tenant->id, $property->id, $key);
                        }
                        $sepConData = [
                            'language_id' => $language->id,
                            'label' => $data,
                            'value' => $request[$language->code . '_value'][$key],
                        ];
                        PropertySpecificationContent::storeSpecificationContent($property_specification->id, $sepConData);
                    }
                }
            }
        });
        Session::flash('success', __('Added successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function updateStatus(Request $request)
    {
        $property = Property::findOrFail($request->propertyId);

        if ($request->status == 1) {
            $property->update(['status' => 1]);
        } else {
            $property->update(['status' => 0]);
        }
        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }
    public function edit($id)
    {
        $tenantID = Auth::guard('web')->user()->id;
        $property = Property::where('user_id', $tenantID)->with('galleryImages')->findOrFail($id);

        $information['property'] = $property;
        $information['propertyContents'] = $property->contents()->get();
        $information['galleryImages'] = $property->galleryImages;

        $information['language'] = $this->defaultLang($tenantID);
        $languages = $this->allLangs($tenantID);
        $information['languages'] = $languages;
        $information['agents'] = Agent::where([['user_id', $tenantID], ['status', 1]])->get();


        $information['propertyCountries'] = Country::where('user_id', 0)
            ->get();
        $information['propertyAmenities'] = PropertyAmenity::where([
            ['property_id', $property->id]
        ])
            ->get();

        $information['propertyStates'] = State::where('user_id', 0)
            ->where('country_id', $property->country_id)
            ->get();
        $information['propertyCities'] = City::where('user_id', 0)
            ->where('state_id', $property->state_id)
            ->get();
        $information['propertyCategories'] = Category::where('user_id', 0)
            ->where([['type', $property->type], ['status', 1]])
            ->get();
        $information['amenities'] = Amenity::where([['user_id', 0], ['status', 1]])->get();


        $information['propertySettings'] = BasicSetting::where('user_id', $tenantID)->select('property_state_status', 'property_country_status')->first();
        $information['specifications'] = PropertySpecification::where('user_id', $tenantID)->where('property_id', $property->id)->get();

        $mapStatus = DB::table('user_basic_settings')->where('user_id', $tenantID)->first()->google_map_api_status;
        if ($mapStatus == 1) {
            $information['propertyAddress'] = PropertyContent::select('address')
                ->where(['property_id' => $id, 'language_id' => $information['language']->id])
                ->first();
        }

        return view('user.property-management.edit', $information);
    }

    public function update(PropertyUpdateRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $tenantID = Auth::guard('web')->user()->id;

            $languages = $this->allLangs($tenantID);
            $property = Property::where('user_id', $tenantID)->findOrFail($request->property_id);

            $featuredImgName = $property->featured_image;
            $floorPlanningImage = $property->floor_planning_image;
            $videoImage = $property->video_image;
            if ($request->hasFile('featured_image')) {
                $featuredImgName = UploadFile::update('assets/img/property/featureds/', $request->featured_image, $property->featured_image);
            }
            if ($request->hasFile('floor_planning_image')) {
                $floorPlanningImage = UploadFile::update('assets/img/property/plannings/', $request->floor_planning_image, $property->floor_planning_image);
            }
            if ($request->hasFile('video_image')) {
                $videoImage = UploadFile::update('assets/img/property/video/', $request->video_image, $property->video_image);
            }
            $requestData = $request->all();
            $requestData['featuredImgName'] = $featuredImgName;
            $requestData['floorPlanningImage'] = $floorPlanningImage;
            $requestData['videoImage'] = $videoImage;
            $property->updateProperty($requestData);

            if ($request->has('amenities')) {
                $property->proertyAmenities()->delete();
                foreach ($request->amenities as $amenity) {
                    PropertyAmenity::create([
                        'user_id' => $tenantID,
                        'property_id' => $property->id,
                        'amenity_id' => $amenity
                    ]);
                }
            }

            $d_property_specifications = PropertySpecification::where('property_id', $request->property_id)->get();
            foreach ($d_property_specifications as $d_property_specification) {
                $d_property_specification_contents = PropertySpecificationContent::where('property_spacification_id', $d_property_specification->id)->get();
                foreach ($d_property_specification_contents as $d_property_specification_content) {
                    $d_property_specification_content->delete();
                }
                $d_property_specification->delete();
            }

            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {

                    $contentData  = [
                        'language_id' => $language->id,
                        'title' => $request[$language->code . '_title'],
                        'slug' => $request[$language->code . '_title'],
                        'summary' => $request[$language->code . '_summary'],
                        'address' => $request[$language->code . '_address'],
                        'description' => $request[$language->code . '_description'],
                        'meta_keyword' => $request[$language->code . '_meta_keyword'],
                        'meta_description' => $request[$language->code . '_meta_description'],
                    ];
                    PropertyContent::updateOrCreatePropertyContent($property->id, $contentData);
                }




                $label_datas = $request[$language->code . '_label'];
                foreach ($label_datas as $key => $data) {
                    if (!empty($request[$language->code . '_value'][$key])) {
                        $property_specification = PropertySpecification::where([['property_id', $property->id], ['key', $key]])->first();
                        if (is_null($property_specification)) {

                            $property_specification = PropertySpecification::storeSpecification($tenantID, $property->id, $key);
                        }

                        $sepConData = [
                            'language_id' => $language->id,
                            'label' => $data,
                            'value' => $request[$language->code . '_value'][$key],
                        ];
                        PropertySpecificationContent::storeSpecificationContent($property_specification->id, $sepConData);
                    }
                }
            }
        });
        Session::flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }



    public function specificationDelete(Request $request)
    {

        $d_project_specification = PropertySpecification::find($request->spacificationId);
        $d_project_specification_contents = PropertySpecificationContent::where('property_spacification_id', $d_project_specification->id)->get();
        foreach ($d_project_specification_contents as $d_project_specification_content) {
            $d_project_specification_content->delete();
        }
        $d_project_specification->delete();
        return Response::json(['status' => 'success'], 200);
    }

    public function delete(Request $request)
    {

        try {
            $tenantId = Auth::guard('web')->user()->id;
            $property = Property::where([['user_id', $tenantId], ['id', $request->property_id]])->first();
            if ($property) {
                $property->destroyPropertry();
            }
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return redirect()->back();
        }

        Session::flash('success', __('Deleted successfully!'));
        return redirect()->back();
    }


    public function bulkDelete(Request $request)
    {

        $propertyIds = $request->ids;
        try {
            foreach ($propertyIds as $id) {
                $tenantId = Auth::guard('web')->user()->id;
                $property = Property::where([['user_id', $tenantId], ['id', $id]])->first();
                if ($property) {
                    $property->destroyPropertry();
                }
            }
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return redirect()->back();
        }
        Session::flash('success', __('Deleted successfully!'));
        return response()->json(['status' => 'success'], 200);
    }
}
