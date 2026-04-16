<?php

namespace App\Http\Controllers\UserFrontend;

use Illuminate\Http\Request;
use App\Models\User\Language;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Models\User\Property\City;
use App\Models\User\Property\State;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Models\User\Property\Amenity;
use App\Models\User\Property\Country;
use App\Jobs\NotifyForPropertyContact;
use App\Models\User\Property\Category;
use App\Models\User\Property\Property;
use Illuminate\Support\Facades\Response;
use App\Models\User\Property\CityContent;
use Illuminate\Support\Facades\Validator;
use App\Models\User\Property\StateContent;
use App\Models\User\Property\AmenityContent;
use App\Models\User\Property\CountryContent;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Models\User\Property\CategoryContent;
use App\Models\User\Property\PropertyAmenity;
use App\Models\User\Property\PropertyContact;
use App\Models\User\Property\PropertyContent;
use App\Models\User\Property\PropertyContanct;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;


class PropertyController extends Controller
{
    use TenantFrontendLanguage, PageHeadings;
    public function index($username, Request $request)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();
        $language = $this->currentLang($tenantId);
        $queryResult['pageHeading'] = $this->pageHeading($tenantId);

        $information['seoInfo'] = $language->seoInfo()->select('meta_keyword_properties', 'meta_description_properties')->first();

        $adminLang = getPropertyspectLang($language);
        if (!is_null($adminLang)) {
            if ($request->has('type') && ($request->type == 'commercial' || $request->type == 'residential')) {
                $information['categories'] = Category::where('user_id', 0)->with(['categoryContent' => function ($q) use ($adminLang) {
                    $q->where('language_id', $adminLang->id);
                }, 'properties'])->where([['status', 1], ['type', $request->type]])->get();
            } else {
                $information['categories'] = Category::where('user_id', 0)->with(['categoryContent' => function ($q) use ($adminLang) {
                    $q->where('language_id', $adminLang->id);
                }, 'properties'])->where('status', 1)->get();
            }
            $information['amenities'] = Amenity::where('user_id', 0)->where('status', 1)->with(['amenityContent' => function ($q) use ($adminLang) {
                $q->where('language_id', $adminLang->id);
            }])->orderBy('serial_number')->get();
        } else {
            $information['categories'] = [];
            $information['amenities'] = [];
        }

        $propertyCategory = null;
        $category = null;
        if ($request->filled('category') && $request->category != 'all' && !is_null($adminLang)) {
            $category = $request->category;
            $propertyCategory = CategoryContent::where('user_id', 0)->where([['language_id', $adminLang->id], ['slug', $category]])->first();
        }

        $amenities = [];
        $amenityInContentId = [];
        if ($request->filled('amenities') && !is_null($adminLang)) {
            $amenities = $request->amenities;
            foreach ($amenities as $amenity) {
                $amenConId = AmenityContent::where('name', $amenity)->where('language_id', $adminLang->id)->pluck('amenity_id')->first();
                array_push($amenityInContentId, $amenConId);
            }
        }

        $amenityInContentId = array_unique($amenityInContentId);
        $type = null;
        if ($request->filled('type') && $request->type != 'all') {
            $type = $request->type;
        }

        $price = null;
        if ($request->filled('price') && $request->price != 'all') {
            $price = $request->price;
        }

        $purpose = null;
        if ($request->filled('purpose') && $request->purpose != 'all') {
            $purpose = $request->purpose;
        }

        $min = $max = null;
        if ($request->filled('min') && $request->filled('max')) {
            $min = intval($request->min);
            $max = intval(($request->max));
        }

        $title = $location = $beds = $baths = $area = $countryId = $stateId = $cityId = null;
        if ($request->filled('country') && $request->filled('country')) {

            $country = CountryContent::where([['name', $request->country], ['language_id', $adminLang->id], ['user_id', 0]])->first();
            if ($country) {
                $countryId = $country->country_id;
            }
        }
        if ($request->filled('state') && $request->filled('state')) {

            $state = StateContent::where([['name', $request->state], ['language_id', $adminLang->id], ['user_id', 0]])->first();
            if ($state) {
                $stateId = $state->state_id;
            }
        }
        if ($request->filled('city') && $request->filled('city')) {

            $city = CityContent::where([['name', $request->city], ['language_id', $adminLang->id], ['user_id', 0]])->first();
            if ($city) {
                $cityId = $city->city_id;
            }
        }
        if ($request->filled('title') && $request->filled('title')) {
            $title =  $request->title;
        }

        if ($request->filled('location') && $request->filled('location')) {
            $location =  $request->location;
        }
        if ($request->filled('beds') && $request->filled('beds')) {
            $beds =  $request->beds;
        }
        if ($request->filled('baths') && $request->filled('baths')) {
            $baths =  $request->baths;
        }
        if ($request->filled('area') && $request->filled('area')) {
            $area =  $request->area;
        }


        if ($request->filled('sort')) {
            if ($request['sort'] == 'new') {
                $order_by_column = 'user_properties.id';
                $order = 'desc';
            } elseif ($request['sort'] == 'old') {
                $order_by_column = 'user_properties.id';
                $order = 'asc';
            } elseif ($request['sort'] == 'high-to-low') {
                $order_by_column = 'user_properties.price';
                $order = 'desc';
            } elseif ($request['sort'] == 'low-to-high') {
                $order_by_column = 'user_properties.price';
                $order = 'asc';
            } else {
                $order_by_column = 'user_properties.id';
                $order = 'desc';
            }
        } else {
            $order_by_column = 'user_properties.id';
            $order = 'desc';
        }

        $property_contents = Property::where([['user_properties.user_id', $tenantId], ['user_properties.status', 1]])
            ->join('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            ->join('user_property_categories', 'user_property_categories.id', 'user_properties.category_id')
            ->where('user_property_contents.language_id', $language->id)


            ->leftJoin('user_agents', 'user_properties.agent_id', '=', 'user_agents.id')
            ->where(function ($query) {
                $query->where('user_properties.agent_id', '=', 0)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('user_properties.agent_id')
                            ->where('user_agents.status', '=', 1);
                    });
            })


            ->when($type, function ($query) use ($type) {
                return $query->where('user_properties.type', $type);
            })
            ->when($purpose, function ($query) use ($purpose) {
                return $query->where('user_properties.purpose', $purpose);
            })
            ->when($countryId, function ($query) use ($countryId) {
                return $query->where('user_properties.country_id', $countryId);
            })
            ->when($stateId, function ($query) use ($stateId) {
                return $query->where('user_properties.state_id', $stateId);
            })
            ->when($cityId, function ($query) use ($cityId) {
                return $query->where('user_properties.city_id', $cityId);
            })
            ->when($category && $propertyCategory, function ($query) use ($propertyCategory) {
                return $query->where('user_properties.category_id', $propertyCategory->category_id);
            })

            ->when(!empty($amenityInContentId), function ($query) use ($amenityInContentId) {
                $query->whereHas(
                    'proertyAmenities',
                    function ($q) use ($amenityInContentId) {
                        $q->whereIn('amenity_id', $amenityInContentId);
                    },
                    '=',
                    count($amenityInContentId)
                );
            })
            ->when($price, function ($query) use ($price) {
                if ($price == 'negotiable') {
                    return $query->where('user_properties.price', null);
                } elseif ($price == 'fixed') {

                    return $query->where('user_properties.price', '!=', null);
                } else {
                    return $query;
                }
            })

            ->when($min, function ($query) use ($min, $max, $price) {
                if ($price == 'fixed' || empty($price)) {
                    return $query->where('user_properties.price', '>=', $min)
                        ->where('user_properties.price', '<=', $max);
                } else {
                    return $query;
                }
            })
            ->when($beds, function ($query) use ($beds) {
                return $query->where('user_properties.beds', $beds);
            })
            ->when($baths, function ($query) use ($baths) {
                return $query->where('user_properties.bath', $baths);
            })
            ->when($area, function ($query) use ($area) {
                return $query->where('user_properties.area', $area);
            })
            ->when($title, function ($query) use ($title) {
                return $query->where('user_property_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->when($location, function ($query) use ($location) {
                return $query->where('user_property_contents.address', 'LIKE', '%' . $location . '%');
            })
            ->with(['categoryContent' => function ($q) use ($language) {
                $q->where('language_id', $language->id);
            }])

            ->select('user_properties.*', 'user_property_categories.id as categoryId', 'user_property_contents.title', 'user_property_contents.slug', 'user_property_contents.address', 'user_property_contents.description', 'user_property_contents.language_id')
            ->orderBy($order_by_column, $order)
            ->paginate(12);

        $information['adminLang'] = $adminLang;
        $information['property_contents'] = $property_contents;
        $information['contents'] = $property_contents;

        $information['all_cities'] = City::where('user_id', 0)->where('status', 1)
            ->with(['cityContent' => function ($q) use ($adminLang) {
                $q->where('language_id', $adminLang->id);
            }])->get();
        $information['all_states'] = State::where('user_id', 0)->with(['stateContent' => function ($q) use ($adminLang) {
            $q->where('language_id', $adminLang->id);
        }])->get();
        $information['all_countries'] = Country::where('user_id', 0)->with(['countryContent' => function ($q) use ($adminLang) {
            $q->where('language_id', $adminLang->id);
        }])->get();

        $min = Property::where([['user_id', $tenantId], ['status', 1]])->min('price');
        $max = Property::where([['user_id', $tenantId], ['status', 1]])->max('price');
        $information['min'] = intval($min);
        $information['max'] = intval($max);
        if ($request->ajax()) {
            $viewContent = View::make('tenant_frontend.property.property',  $information);
            $viewContent = $viewContent->render();

            return response()->json(['propertyContents' => $viewContent, 'properties' => $property_contents])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        return view('tenant_frontend.property.index', $information);
    }

    public function details($username, $slug)
    {
        $user = getUser();
        $tenantId = $user->id;
        // $misc = new MiscellaneousController();
        $language = $this->currentLang($tenantId);
        $defaultLanguage = $this->defaultLang($tenantId);

        $property = PropertyContent::where('slug', $slug)->whereHas(
            'property',
            function ($q) use ($tenantId) {
                $q->where('status', 1)
                    ->where('user_id', $tenantId);
            }
        )->select('property_id', 'language_id', 'id')->firstOrFail();

        if (!is_null($property)) {

            $content =  PropertyContent::where('property_id', $property->property_id)->where('language_id', $language->id)->select('property_id', 'language_id', 'id')->first();

            if (is_null($content)  && $language->id !== $defaultLanguage->id) {
                $language = $defaultLanguage;
                $content =  PropertyContent::where('property_id', $property->property_id)->where('language_id', $language->id)->select('property_id', 'language_id', 'id')->first();
            }
        }

        $property = PropertyContent::query()
            ->where('user_property_contents.property_id', $content->property_id)
            ->where('user_property_contents.language_id', $language->id)
            ->leftJoin('user_properties', 'user_property_contents.property_id', 'user_properties.id')
            ->leftJoin('user_agents', 'user_properties.agent_id', '=', 'user_agents.id')
            ->where(function ($query) {
                $query->where('user_properties.agent_id', '=', 0)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('user_properties.agent_id')
                            ->where('user_agents.status', '=', 1);
                    });
            })
            ->with(['propertySpacifications', 'galleryImages'])
            ->select('user_properties.*', 'user_property_contents.*', 'user_properties.id as propertyId', 'user_property_contents.id as contentId')
            ->firstOrFail();


        $information['propertyContent'] = $property;
        $information['language'] = $language;
        $information['sliders'] =  $property->galleryImages;



        $information['user'] = $user;
        $information['agent'] = Agent::with(['agentInfo' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->find($property->agent_id);


        //get property categories and amenities those added by admin
        $adminLang = getPropertyspectLang($language);
        $categories = Category::where('status', 1)->with(['categoryContent' => function ($q) use ($adminLang) {
            $q->where('language_id', $adminLang->id);
        }])->get();
        $information['amenities'] = PropertyAmenity::with(['amenityContent' => function ($q) use ($adminLang) {
            $q->where('language_id', $adminLang->id);
        }])->where('property_id', $property->property_id)->get();



        $categories->map(function ($category) {
            $category['propertiesCount'] = $category->properties()->where([['status', 1], ['approve_status', 1]])->count();
        });
        $information['categories'] = $categories;

        $information['relatedProperty'] = Property::where([['user_properties.status', 1], ['user_properties.approve_status', 1]])
            ->leftJoin('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            ->leftJoin('user_agents', 'user_properties.agent_id', '=', 'user_agents.id')
            ->where(function ($query) {
                $query->where('user_properties.agent_id', '=', 0)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('user_properties.agent_id')
                            ->where('user_agents.status', '=', 1);
                    });
            })
            ->where([['user_properties.id', '!=', $property->property_id], ['user_properties.category_id', $property->category_id]])
            ->where('user_property_contents.language_id', $language->id)->latest('user_properties.created_at')
            ->select('user_properties.*', 'user_property_contents.title', 'user_property_contents.slug', 'user_property_contents.address', 'user_property_contents.language_id')
            ->take(5)->get();

        $information['adminLang'] = $adminLang;

        return view('tenant_frontend.property.details', $information);
    }

    public function contact($website, Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns',
            'phone' => 'required|numeric',
            'message' => 'required'
        ];
        $user = getUser();

        $info = BasicSetting::where('user_id', $user->id)->select('google_recaptcha_status')->first();
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

        if (!empty($request->agent_id)) {
            $agent = Agent::find($request->agent_id);
            if (empty($agent)) {
                return back()->with('error', __('Something went wrong!'));
            }
            $request['to_mail'] = $agent->email;
        } else {
            $request['to_mail'] = $user->email;
        }

        $agentId = (!empty($request->agent_id) && $request->agent_id != 0) ? $request->agent_id : 0;

        try {
            $contact = new PropertyContact();
            $contact->createContact($user->id, $agentId, $request);

            NotifyForPropertyContact::dispatch($user->id, $request->all());
        } catch (\Exception $e) {

            return back()->with('error', __('Something went wrong!'));
        }



        return back()->with('success', __('Message sent successfully'));
    }

    public function getStateCities($username, Request $request)
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);

        $adminLang = getPropertyspectLang($language);


        $states = State::where('country_id', $request->id)
            ->with(['cities', 'stateContent' => function ($q) use ($adminLang) {
                $q->where('language_id', $adminLang->id);
            }])->get();

        $cities = City::where('country_id', $request->id)
            ->with(['cityContent' => function ($q) use ($adminLang) {
                $q->where('language_id', $adminLang->id);
            }])->orderBy('serial_number', 'asc')->get();

        return Response::json(['states' => $states, 'cities' => $cities], 200);
    }

    public function getCities($username, Request $request)
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);

        $adminLang = getPropertyspectLang($language);

        $cities = City::where([['user_id', 0], ['state_id', $request->state_id]])
            ->where('status', 1)->with(['cityContent' => function ($q) use ($adminLang) {
                $q->where('language_id', $adminLang->id);
            }])->orderBy('serial_number', 'asc')->get();
        return Response::json(['cities' => $cities], 200);
    }

    public function getCategories($username, Request $request)
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);

        $adminLang = getPropertyspectLang($language);


        if ($request->type != 'all') {
            $categories = Category::where([['type', $request->type], ['status', 1], ['user_id', 0]])
                ->with(['categoryContent' => function ($q) use ($adminLang) {
                    $q->where('language_id', $adminLang->id);
                }])->select('id', 'type')->orderBy('serial_number', "ASC")->get();
        } else {
            $categories = Category::where([['status', 1], ['user_id', 0]])
                ->with(['categoryContent' => function ($q) use ($adminLang) {
                    $q->where('language_id', $adminLang->id);
                }])->select('id', 'type')->orderBy('serial_number', "ASC")->get();
        }

        return Response::json(['categories' => $categories], 200);
    }
}
