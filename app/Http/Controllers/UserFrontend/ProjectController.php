<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyForProjectContact;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Models\User\Project\Category;
use App\Models\User\Project\CategoryContent;
use App\Models\User\Project\City;
use App\Models\User\Project\CityContent;
use App\Models\User\Project\Contact;
use App\Models\User\Project\Country;
use App\Models\User\Project\CountryContent;
use App\Models\User\Project\Project;
use App\Models\User\Project\ProjectContent;
use App\Models\User\Project\State;
use App\Models\User\Project\StateContent;
use Illuminate\Http\Request;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class ProjectController extends Controller
{
    use TenantFrontendLanguage, PageHeadings;
    public function index($username, Request $request)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();
        $language = $this->currentLang($tenantId);
        $information['seoInfo'] = $language->seoInfo()->select('meta_keyword_projects', 'meta_description_projects')->first();
        $information['breadcrumb'] = $misc->getBreadcrumb($tenantId);
        $queryResult['pageHeading'] = $this->pageHeading($tenantId);



        $projectCategory = null;
        $category = null;
        if ($request->filled('category') && $request->category != 'all') {
            $category = $request->category;
            $projectCategory = CategoryContent::where('user_id', $tenantId)->where([['language_id', $language->id], ['slug', $category]])->first();
        }

        $min = $max = null;
        if ($request->filled('min') && $request->filled('max')) {
            $min = intval($request->min);
            $max = intval(($request->max));
        }

        $title = $location = $countryId = $stateId = $cityId = null;
        if ($request->filled('country') && $request->filled('country')) {

            $country = CountryContent::where([['name', $request->country], ['language_id', $language->id], ['user_id', $tenantId]])->first();
            if ($country) {
                $countryId = $country->country_id;
            }
        }
        if ($request->filled('state') && $request->filled('state')) {

            $state = StateContent::where([['name', $request->state], ['language_id', $language->id], ['user_id', $tenantId]])->first();
            if ($state) {
                $stateId = $state->state_id;
            }
        }
        if ($request->filled('city') && $request->filled('city')) {

            $city = CityContent::where([['name', $request->city], ['language_id', $language->id], ['user_id', $tenantId]])->first();
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


        if ($request->filled('sort')) {
            if ($request['sort'] == 'new') {
                $order_by_column = 'user_projects.id';
                $order = 'desc';
            } elseif ($request['sort'] == 'old') {
                $order_by_column = 'user_projects.id';
                $order = 'asc';
            } elseif ($request['sort'] == 'high-to-low') {
                $order_by_column = 'user_projects.min_price';
                $order = 'desc';
            } elseif ($request['sort'] == 'low-to-high') {
                $order_by_column = 'user_projects.min_price';
                $order = 'asc';
            } else {
                $order_by_column = 'user_projects.id';
                $order = 'desc';
            }
        } else {
            $order_by_column = 'user_projects.id';
            $order = 'desc';
        }

        $projects  = Project::where('user_projects.user_id', $tenantId)
            ->join('user_project_contents', 'user_projects.id', 'user_project_contents.project_id')

            ->leftJoin('user_agents', 'user_projects.agent_id', '=', 'user_agents.id')
            ->where(function ($query) {
                $query->where('user_projects.agent_id', '=', 0)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('user_projects.agent_id')
                            ->where('user_agents.status', '=', 1);
                    });
            })

            ->where('user_project_contents.language_id', $language->id)
            ->when($title, function ($query) use ($title) {
                return $query->where('user_project_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->when($location, function ($query) use ($location) {
                return $query->where('user_project_contents.address', 'LIKE', '%' . $location . '%');
            })
            ->when($min, function ($query) use ($min) {
                return $query->where('user_projects.min_price', '>=', $min);
            })
            ->when($max, function ($query) use ($max) {
                return $query->where('user_projects.max_price', '<=', $max);
            })
            ->when($category && $projectCategory, function ($query) use ($projectCategory) {
                return $query->where('user_projects.category_id', $projectCategory->category_id);
            })
            ->when($countryId, function ($query) use ($countryId) {
                return $query->where('user_projects.country_id', $countryId);
            })
            ->when($stateId, function ($query) use ($stateId) {
                return $query->where('user_projects.state_id', $stateId);
            })
            ->when($cityId, function ($query) use ($cityId) {
                return $query->where('user_projects.city_id', $cityId);
            })
            ->select('user_projects.*',  'user_project_contents.title', 'user_project_contents.slug', 'user_project_contents.address')
            ->orderBy($order_by_column, $order)
            ->paginate(12);
        $information['projects'] = $projects;
        $information['contents'] = $projects;



        $information['categories'] = Category::where('user_id', $tenantId)->with(['categoryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }, 'projects'])->where('status', 1)->get();

        $information['all_cities'] = City::where('user_id', $tenantId)->where('status', 1)->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['all_states'] = State::where('user_id', $tenantId)->with(['stateContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['all_countries'] = Country::where('user_id', $tenantId)->with(['countryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();

        $min = Project::where('user_id', $tenantId)->min('min_price');
        $max = Project::where('user_id', $tenantId)->max('max_price');
        $information['min'] = intval($min);
        $information['max'] = intval($max);

        if ($request->ajax()) {
            $viewContent = View::make('tenant_frontend.project.project',  $information);
            $viewContent = $viewContent->render();

            return response()->json(['projectContents' => $viewContent, 'projects' => $projects])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        return view('tenant_frontend.project.index', $information);
    }

    public function details($username, Request $request, $slug)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();
        $language = $this->currentLang($tenantId);
        $defaultLanguage = $this->defaultLang($tenantId);
       
        $information['breadcrumb'] = $misc->getBreadcrumb($tenantId);
        $projectIds  = ProjectContent::where('slug', $slug)->where('language_id', $language->id)->whereHas('project', function ($q) use ($tenantId) {
            $q->where('user_id', $tenantId);
        })->pluck('project_id');

    
        // // if language content not found, then it gives default language data 
        if (count($projectIds) ==  0) {
            $projectIds  = ProjectContent::where('slug', $slug)->whereHas('project', function ($q) use ($tenantId) {
                $q->where('user_id', $tenantId);
            })->pluck('project_id');
            $language = $defaultLanguage;
        }
        
        $project = Project::query()
            ->where('user_projects.user_id', $tenantId)
            ->whereIn('user_projects.id', $projectIds)
            ->where('user_project_contents.language_id', $language->id)
            ->join('user_project_contents', 'user_projects.id', 'user_project_contents.project_id')

            ->leftJoin('user_agents', 'user_projects.agent_id', '=', 'user_agents.id')
            ->where(function ($query) {
                $query->where('user_projects.agent_id', '=', 0)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('user_projects.agent_id')
                            ->where('user_agents.status', '=', 1);
                    });
            })
            ->select('user_projects.*', 'user_project_contents.id as contentId', 'user_project_contents.title', 'user_project_contents.slug', 'user_project_contents.address', 'user_project_contents.language_id', 'user_project_contents.description', 'user_project_contents.meta_keyword', 'user_project_contents.meta_description')

            ->with(['projectTypes', 'galleryImages', 'projectTypeContents' => function ($q) use ($language) {
                $q->where('language_id', $language->id);
            }, 'floorplanImages', 'specifications'])
            ->firstOrFail();
        $information['language'] = $language;
        $information['project'] = $project;
        $information['floorPlanImages'] = $information['project']->floorplanImages;
        $information['galleryImages'] =  $information['project']->galleryImages;

        return view('tenant_frontend.project.details', $information);
    }

    private function project() {}

    public function getCategories($username,  Request $request)
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);

        $categories = Category::where([['status', 1], ['user_id', $tenantId]])->with(['categoryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->select('id', 'type')->orderBy('serial_number', "ASC")->get();

        return Response::json(['categories' => $categories], 200);
    }

    public function getStateCities($username, Request $request)
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);
        $states = State::where('country_id', $request->id)->with(['cities', 'stateContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $cities = City::where('country_id', $request->id)->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->orderBy('serial_number', 'asc')->get();
        return Response::json(['states' => $states, 'cities' => $cities], 200);
    }

    public function getCities($username, Request $request)
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);
        $cities = City::where([['user_id', $tenantId], ['state_id', $request->state_id]])->where('status', 1)->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->orderBy('serial_number', 'asc')->get();
        return Response::json(['cities' => $cities], 200);
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
            $contact = new Contact();
            $contact->createContact($user->id, $agentId, $request);

            NotifyForProjectContact::dispatch($user->id, $request->all());
        } catch (\Exception $e) {
            return back()->with('error', __('Something went wrong!'));
        }



        return back()->with('success', 'Message sent successfully');
    }
}
