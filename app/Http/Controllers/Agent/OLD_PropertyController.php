<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Helpers\UploadFile;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Models\User\Property\City;
use Illuminate\Support\Facades\DB;
use App\Jobs\NotifyUserForProperty;
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
use App\Models\User\Property\PropertyAmenity;
use App\Models\User\Property\PropertyContact;
use App\Models\User\Property\PropertyContent;
use App\Models\User\Property\PropertySpecification;
use App\Models\User\Property\PropertySpecificationContent;
use App\Http\Requests\PropertyManagement\PropertyStoreRequest;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Http\Requests\PropertyManagement\PropertyUpdateRequest;

class PropertyController extends Controller
{
    use TenantFrontendLanguage;
    public function type($username, Request $request)
    {
        $data['commertialCount'] = Property::where([['type', 'commercial'], ['agent_id', Auth::guard('agent')->user()->id]])->count();
        $data['residentialCount'] = Property::where([['type', 'residential'], ['agent_id', Auth::guard('agent')->user()->id]])->count();
        return view('agent.property.type', $data);
    }

    public function index($username, Request $request)
    {
        $agent =  Auth::guard('agent')->user();
        $tenantId =  $agent->user_id;

        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->currentLang($tenantId);
        }

        $language_id = $language->id;
        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }

        $data['properties'] = Property::where('agent_id', $agent->id)
            ->leftJoin('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            ->when($title, function ($query) use ($title) {
                return $query->where('user_property_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->where('user_property_contents.language_id', $language_id)
            ->select(
                'user_properties.id',
                'user_properties.type',
                'user_properties.agent_id',
                'user_properties.status',
                'user_properties.featured',
                'user_properties.city_id',
                'user_property_contents.title'
            )
            ->orderBy('user_properties.id', 'desc')
            ->paginate(10);

        return view('agent.property.index', $data);
    }

    public function create($username, Request $request)
    {
        $information = [];
        $agent = Auth::guard('agent')->user();
        $tenantId = $agent->user_id;

        $languages = $this->allLangs($tenantId);

        $information['tenantLangs'] = $languages;
        $information['propertyCategories'] = Category::where('user_id', 0)
            ->where([['type', $request->type], ['status', 1]])
            ->get();

        $information['propertyCountries'] = Country::where('user_id', 0)->get();
        $information['amenities'] = Amenity::where('user_id', 0)->where('status', 1)->get();
        $information['states'] = State::where('user_id', 0)->get();
        $information['cities'] = City::where('user_id', 0)->where('status', 1)->get();

        $information['userBs'] = BasicSetting::where('user_id', $tenantId)->first();

        return view('agent.property.create', $information);
    }
    public function updateFeatured(Request $request)
    {


        return redirect()->back();
    }

    public function imagesstore($username, Request $request)
    {
        $img = $request->file('file');
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg', 'webp');
        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    $ext = $img->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail(__("Only png, jpg, jpeg images are allowed"));
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
        $pi->user_id = Auth::guard('agent')->user()->user_id;
        $pi->image = $imageName;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }
    public function imagermv($username, Request $request)
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
    public function imagedbrmv($username, Request $request)
    {

        $pi = SliderImage::find($request->fileid);
        $imageCount = SliderImage::where('property_id', $pi->property_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/property/slider-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }
    public function store($username, PropertyStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $agent = Agent::findorFail(Auth::guard('agent')->user()->id);

            $featuredImgURL = $request->featured_image;
            if (request()->hasFile('featured_image')) {
                $featuredImgName = UploadFile::store('assets/img/property/featureds', $featuredImgURL);
            }
            $tenantId = $agent->user_id;
            $languages = $this->allLangs($tenantId);

            $floorPlanningImage = null;
            $videoImage = null;
            if (request()->hasFile('floor_planning_image')) {
                $floorPlanningImage = UploadFile::store('assets/img/property/plannings', $request->floor_planning_image);
            }
            if ($request->hasFile('video_image')) {
                $videoImage = UploadFile::store('assets/img/property/video/', $request->video_image);
            }

            $property = Property::create([
                'user_id' => $agent->user_id,
                'agent_id' =>  $agent->id,
                'category_id' => $request->category_id,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'featured_image' => $featuredImgName,
                'floor_planning_image' => $floorPlanningImage,
                'video_image' => $videoImage,
                'price' => $request->price,
                'purpose' => $request->purpose,
                'type' => $request->type,
                'beds' => $request->beds,
                'bath' => $request->bath,
                'area' => $request->area,
                'video_url' => $request->video_url,
                'status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'currency_symbol' => $request->currency_symbol,
            ]);

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
                        'user_id' => $agent->user_id,
                    ]);
                }
            }

            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                    $propertyContent = new PropertyContent();
                    $propertyContent->language_id = $language->id;
                    $propertyContent->property_id = $property->id;
                    $propertyContent->title = $request[$language->code . '_title'];
                    $propertyContent->slug = $request[$language->code . '_title'];
                    $propertyContent->address = $request[$language->code . '_address'];
                    $propertyContent->description = $request[$language->code . '_description'];
                    $propertyContent->meta_keyword = $request[$language->code . '_meta_keyword'];
                    $propertyContent->meta_description = $request[$language->code . '_meta_description'];
                    $propertyContent->save();
                }
                $label_datas = $request[$language->code . '_label'];
                foreach ($label_datas as $key => $data) {
                    if (!empty($request[$language->code . '_value'][$key])) {
                        $property_specification = PropertySpecification::where([['property_id', $property->id], ['key', $key]])->first();
                        if (is_null($property_specification)) {
                            $property_specification = new PropertySpecification();
                            $property_specification->property_id = $property->id;
                            $property_specification->key  = $key;
                            $property_specification->user_id = $agent->user_id;
                            $property_specification->save();
                        }
                        $property_specification_content = new PropertySpecificationContent();
                        $property_specification_content->language_id = $language->id;
                        $property_specification_content->property_spacification_id = $property_specification->id;
                        $property_specification_content->label = $data;
                        $property_specification_content->value = $request[$language->code . '_value'][$key];
                        $property_specification_content->save();
                    }
                }
            }
            $propertyContent = PropertyContent::where('property_id', $property->id)->select('title')->first();

            NotifyUserForProperty::dispatch($agent->user_id, $agent, $propertyContent->title);
        });
        Session::flash('success', __('Added successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function updateStatus($username, Request $request)
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
    public function edit($usename, $id)
    {
        $tenantId = Auth::guard('agent')->user()->user_id;
        $property = Property::with('galleryImages')->where('agent_id', Auth::guard('agent')->user()->id)->findOrFail($id);
        $information['property'] = $property;
        $information['galleryImages'] = $property->galleryImages;

        $information['tenantFrontLangs'] = $this->allLangs($tenantId);

        $language = $this->defaultLang($tenantId);
        $information['language'] = $language;

        // $information['propertyCategories'] = Category::where([['type', $property->type], ['status', 1], ['user_id', $tenantId]])->with(['categoryContent' => function ($q) use ($language) {
        //     $q->where('language_id', $language->id);
        // }])->get();
        $information['propertyCategories'] = Category::where('user_id', 0)
            ->where([['type', $property->type], ['status', 1]])
            ->get();
        $information['states'] = State::getStates($tenantId, $language->id);


        $information['propertyCountries'] = Country::where('user_id', 0)->get();
        $information['propertyStates'] = State::where('user_id', 0)->where('country_id', $property->country_id)->get();

        $information['propertyCities'] = City::where('user_id', 0)->where('state_id', $property->state_id)->get();

        $information['propertyAmenities'] = PropertyAmenity::where('property_id', $property->id)->get();

        $information['amenities'] = Amenity::where([['user_id', 0], ['status', 1]])->get();
        $information['specifications'] = PropertySpecification::where('property_id', $property->id)->get();
        $information['userBs'] = BasicSetting::where('user_id', $tenantId)->first();
        return view('agent.property.edit', $information);
    }

    public function update($username, PropertyUpdateRequest $request,  $id)
    {

        DB::transaction(
            function () use ($request, $id) {
                $agent = Auth::guard('agent')->user();
                $tenantId = $agent->user_id;
                $languages = $this->allLangs($tenantId);

                $property = Property::where('agent_id', $agent->id)->findOrFail($request->property_id);


                $featuredImgName = $property->featured_image;
                $floorPlanningImage = $property->floor_planning_image;
                $videoImage = $property->video_image;

                if ($request->hasFile('featured_image')) {
                    $featuredImgName = UploadFile::update('assets/img/property/featureds/', $request->featured_image, $property->featured_image);
                }

                if ($request->hasFile('floor_planning_image')) {

                    $floorPlanningImage = UploadFile::update('assets/img/property/plannings', $request->floor_planning_image, $property->floor_planning_image);
                }
                if ($request->hasFile('video_image')) {
                    $videoImage = UploadFile::update('assets/img/property/video/', $request->video_image, $property->video_image);
                }

                $property->update([
                    'category_id' => $request->category_id,
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'featured_image' => $featuredImgName,
                    'floor_planning_image' => $floorPlanningImage,
                    'video_image' => $videoImage,
                    'price' => $request->price,
                    'purpose' => $request->purpose,
                    'type' => $request->type,
                    'beds' => $request->beds,
                    'bath' => $request->bath,
                    'area' => $request->area,
                    'video_url' => $request->video_url,
                    'status' => $request->status,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'currency_symbol' => $request->currency_symbol,
                ]);

                $d_property_specifications = PropertySpecification::where('property_id', $request->property_id)->get();
                foreach ($d_property_specifications as $d_property_specification) {

                    $d_property_specification->deleteSpecification();
                }

                if ($request->has('amenities')) {
                    $property->proertyAmenities()->delete();
                    foreach ($request->amenities as $amenity) {
                        PropertyAmenity::create([
                            'property_id' => $property->id,
                            'amenity_id' => $amenity,
                            'user_id' => $agent->user_id
                        ]);
                    }
                }


                foreach ($languages as $language) {
                    if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                        $propertyContent =  PropertyContent::where('property_id', $request->property_id)->where('language_id', $language->id)->first();

                        if (empty($propertyContent)) {
                            $propertyContent = new PropertyContent();
                        }

                        $propertyContent->language_id = $language->id;
                        $propertyContent->property_id = $property->id;
                        $propertyContent->title = $request[$language->code . '_title'];
                        $propertyContent->slug = $request[$language->code . '_title'];
                        $propertyContent->address = $request[$language->code . '_address'];
                        $propertyContent->description = $request[$language->code . '_description'];
                        $propertyContent->meta_keyword = $request[$language->code . '_meta_keyword'];
                        $propertyContent->meta_description = $request[$language->code . '_meta_description'];
                        $propertyContent->save();
                    }
                    $label_datas = $request[$language->code . '_label'];
                    foreach ($label_datas as $key => $data) {
                        if (!empty($request[$language->code . '_value'][$key])) {
                            $property_specification = PropertySpecification::where([['property_id', $property->id], ['key', $key]])->first();
                            if (is_null($property_specification)) {
                                $property_specification = new PropertySpecification();
                                $property_specification->property_id = $property->id;
                                $property_specification->user_id = $agent->user_id;
                                $property_specification->key  = $key;
                                $property_specification->save();
                            }
                            $property_specification_content = new PropertySpecificationContent();
                            $property_specification_content->language_id = $language->id;
                            $property_specification_content->property_spacification_id = $property_specification->id;
                            $property_specification_content->label = $data;
                            $property_specification_content->value = $request[$language->code . '_value'][$key];
                            $property_specification_content->save();
                        }
                    }
                }
            }
        );
        Session::flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function messages($username, Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $tanant_id = $agent->user_id;
        if ($request->has('language')) {
            $language = $this->selectLang($tanant_id, $request->language);
        } else {
            $language = $this->defaultLang($tanant_id);
        }

        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }

        $messages = PropertyContact::where('user_property_contacts.agent_id', $agent->id)
            ->leftJoin('user_properties', 'user_property_contacts.property_id', 'user_properties.id')
            ->leftJoin('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            ->where('user_property_contents.language_id', $language->id)
            ->when($title, function ($query) use ($title) {
                return $query->where('user_property_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->select('user_property_contacts.*', 'user_property_contents.title', 'user_property_contents.slug')
            ->latest()->get();


        return view('agent.property.message', compact('messages'));
    }
    public function destroyMessage(Request $request, $username)
    {
        $message = PropertyContact::where('agent_id', Auth::guard('agent')->user()->id)->find($request->message_id);
        if ($message) {
            $message->delete();
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('Something went wrong!'));
        }
        return redirect()->back();
    }

    public function getStateCities(Request $request, $username)
    {
        $states = State::where('country_id', $request->id)->select('id')->get();
        $states->map(function ($state) {
            $state->name = $state->contents()->first()->name;
        });

        $cities = City::where('country_id', $request->id)
            ->where('status', 1)
            ->select('id')
            ->orderBy('serial_number', 'ASC')
            ->get();
        $cities->map(function ($city) {
            $city->name = $city->contents()->first()->name;
        });

        return Response::json(['states' => $states, 'cities' => $cities], 200);
    }


    public function getCities(Request $request, $username)
    {
        $cities = City::where([['state_id', $request->state_id], ['user_id', 0]])
            ->select('id')
            ->orderBy('serial_number', 'ASC')
            ->get();


        $cities->map(function ($city) {
            $city->name = $city->contents()->first()->name;
        });
        return Response::json(['cities' => $cities], 200);
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
    public function specificationDelete(Request $request, $username)
    {
        try {
            $specification = PropertySpecification::find($request->spacificationId);
            $specification->deleteSpecification();

            return Response::json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error'], 400);
        }
    }

    public function delete($username, Request $request)
    {
        try {
            $agentId = Auth::guard('agent')->user()->id;
            $property = Property::where([['agent_id', $agentId], ['id', $request->property_id]])->first();
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


    public function bulkDelete($username, Request $request)
    {

        $propertyIds = $request->ids;
        try {
            foreach ($propertyIds as $id) {
                $agentId = Auth::guard('agent')->user()->id;
                $property = Property::where([['agent_id', $agentId], ['id', $id]])->first();
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
