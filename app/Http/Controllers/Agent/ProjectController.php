<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\ProjectManagment\ProjectStoreRequest;
use App\Http\Requests\ProjectManagment\ProjectUpdateRequest;
use App\Jobs\NotifyUserForProject;
use App\Models\User\Project\Category;
use App\Models\User\Project\City;
use App\Models\User\Project\Contact;
use App\Models\User\Project\Country;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Models\User\Project\Project;
use App\Models\User\Project\ProjectContent;
use App\Models\User\Project\ProjectFloorplanImage;
use App\Models\User\Project\ProjectGalleryImage;
use App\Models\User\Project\ProjectSpecification;
use App\Models\User\Project\ProjectSpecificationContent;
use App\Models\User\Project\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;


class ProjectController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request, $username)
    {
        $agent =  Auth::guard('agent')->user();
        $tenantId =  $agent->user_id;

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

        $data['projects'] = Project::where('agent_id', $agent->id)
            ->leftJoin('user_project_contents', 'user_projects.id', 'user_project_contents.project_id')
            ->when($title, function ($query) use ($title) {
                return $query->where('user_project_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->where('user_project_contents.language_id', $language_id)
            ->leftJoin('user_project_category_contents', function ($join) use ($language_id) {
                $join->on('user_projects.category_id', '=', 'user_project_category_contents.category_id')
                    ->where('user_project_category_contents.language_id', '=', $language_id);
            })
            ->select(
                'user_projects.id',
                'user_projects.featured',
                'user_projects.complete_status',
                'user_projects.agent_id',
                'user_project_contents.title',
                'user_project_category_contents.name as category_name'
            )
            ->orderBy('user_projects.id', 'desc')
            ->paginate(10);

        return view('agent.project.index', $data);
    }

    public function create(Request $request, $username)
    {
        $information = [];
        $agent = Auth::guard('agent')->user();
        $tenantId = $agent->user_id;

        $language = $this->defaultLang($tenantId);
        $languages = $this->allLangs($tenantId);
        $information['tenantLangs'] = $languages;

        $information['projectCategories'] = Category::getCategories($tenantId, $language->id);
        $information['projectCountries'] = Country::getCountries($tenantId, $language->id);
        //  where('user_id', $tenantId)->with(['countryContent' => function ($q) use ($language) {
        //     $q->where('language_id', $language->id);
        // }])->get();
        $information['states'] = State::getStates($tenantId, $language->id);
        //     where('user_id', $tenantId)->with(['stateContent' => function ($q) use ($language) {
        //     $q->where('language_id', $language->id);
        // }])->get();
        $information['cities'] = City::getCities($tenantId, $language->id);
        //     where([['status', 1], ['user_id', $tenantId]])->with(['cityContent' => function ($q) use ($language) {
        //     $q->where('language_id', $language->id);
        // }])->get();
        // $information['aminities'] = ProjectAmenities::get();
        return view('agent.project.create', $information);
    }
    public function getStateCities(Request $request, $username)
    {
        $tenantId = Auth::guard('agent')->user()->user_id;
        $language = $this->defaultLang($tenantId);

        $states = State::where('country_id', $request->id)->with(['cities', 'stateContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $states->map(function ($state) use ($language) {
            $state->name = $state->getContent($language->id)->name;
        });

        $cities = City::where('country_id', $request->id)->where('status', 1)->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $cities->map(function ($city) use ($language) {
            $city->name = $city->getContent($language->id)->name;
        });

        return Response::json(['states' => $states, 'cities' => $cities], 200);
    }
    public function getCities(Request $request, $username)
    {
        $tenantId = Auth::guard('agent')->user()->user_id;
        $language = $this->defaultLang($tenantId);
        $cities = City::where('state_id', $request->state_id)->where('status', 1)->with(['cityContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $cities->map(function ($city) use ($language) {
            $city->name = $city->getContent($language->id)->name;
        });
        return Response::json(['cities' => $cities], 200);
    }
    public function galleryImagesStore(Request $request, $username)
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
        $imageName = UploadFile::store('assets/img/project/gallery-images/', $request->file('file'));

        $pi = new ProjectGalleryImage();
        if (!empty($request->project_id)) {
            $pi->project_id = $request->project_id;
        }
        $pi->user_id = Auth::guard('agent')->user()->user_id;
        $pi->image = $imageName;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }

    public function galleryImageRmv(Request $request, $username)
    {
        $pi = ProjectGalleryImage::findOrFail($request->fileid);
        $imageCount = ProjectGalleryImage::where('project_id', $pi->project_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/project/gallery-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }

    //imagedbrmv
    public function galleryImageDbrmv(Request $request, $username)
    {
        $pi = ProjectGalleryImage::findOrFail($request->fileid);
        $imageCount = ProjectGalleryImage::where('project_id', $pi->project_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/project/gallery-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }


    public function floorPlanImagesStore(Request $request, $username)
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
        $imageName = UploadFile::store('assets/img/project/floor-paln-images/', $request->file('file'));
        $userId = Auth::guard('agent')->user()->user_id;
        $pi = new ProjectFloorplanImage();
        if (!empty($request->project_id)) {
            $pi->project_id = $request->project_id;
        }
        $pi->image = $imageName;
        $pi->user_id = $userId;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }
    public function floorPlanImageRmv(Request $request, $username)
    {
        $pi = ProjectFloorplanImage::findOrFail($request->fileid);
        $imageCount = ProjectFloorplanImage::where('project_id', $pi->project_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/project/floor-paln-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }


    //imagedbrmv
    public function floorPlanImageDbrmv(Request $request, $username)
    {
        $pi = ProjectFloorplanImage::findOrFail($request->fileid);
        $imageCount = ProjectFloorplanImage::where('project_id', $pi->project_id)->get()->count();
        if ($imageCount > 1) {
            @unlink(public_path('assets/img/project/floor-paln-images/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }


    public function store(ProjectStoreRequest $request, $username)
    {
        DB::transaction(function () use ($request) {

            $featuredImgURL = $request->featured_image;
            if (request()->hasFile('featured_image')) {
                $featuredImgName = UploadFile::store('assets/img/project/featured', $featuredImgURL);
            }
            $agent = Auth::guard('agent')->user();


            $languages = $this->allLangs($agent->user_id);

            $project = Project::create([
                'user_id' =>  $agent->user_id,
                'agent_id' => $agent->id,
                'category_id' => $request->category_id,
                'country_id' => $request->country_id ?? null,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id,
                'featured_image' => $featuredImgName,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'featured' => 0,
                'complete_status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $gallery_images = $request->gallery_images;
            if ($gallery_images) {
                $pis = ProjectGalleryImage::findOrFail($gallery_images);
                foreach ($pis as $key => $pi) {
                    $pi->project_id = $project->id;
                    $pi->user_id = $agent->user_id;
                    $pi->save();
                }
            }

            $floor_plan_images = $request->floor_plan_images;
            if ($floor_plan_images) {
                $pis = ProjectFloorplanImage::findOrFail($floor_plan_images);
                foreach ($pis as $key => $pi) {
                    $pi->project_id = $project->id;
                    $pi->user_id = $agent->user_id;
                    $pi->save();
                }
            }

            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                    $projectContent = new ProjectContent();
                    $projectContent->language_id = $language->id;
                    $projectContent->project_id = $project->id;
                    $projectContent->title = $request[$language->code . '_title'];
                    $projectContent->slug = $request[$language->code . '_title'];

                    $projectContent->address = $request[$language->code . '_address'];
                    $projectContent->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
                    $projectContent->meta_keyword = $request[$language->code . '_meta_keyword'];
                    $projectContent->meta_description = $request[$language->code . '_meta_description'];
                    $projectContent->save();
                }
                $label_datas = $request[$language->code . '_label'];
                foreach ($label_datas as $key => $data) {
                    if (!empty($request[$language->code . '_value'][$key])) {
                        $project_specification = ProjectSpecification::where([['project_id', $project->id], ['key', $key]])->first();
                        if (is_null($project_specification)) {
                            $project_specification = new ProjectSpecification();
                            $project_specification->project_id = $project->id;
                            $project_specification->user_id = $agent->user_id;
                            $project_specification->key  = $key;
                            $project_specification->save();
                        }
                        $project_specification_content = new ProjectSpecificationContent();
                        $project_specification_content->language_id = $language->id;
                        $project_specification_content->project_spacification_id = $project_specification->id;
                        $project_specification_content->label = $data;
                        $project_specification_content->value = $request[$language->code . '_value'][$key];
                        $project_specification_content->save();
                    }
                }
            }
            $projectContent = ProjectContent::where('project_id', $project->id)->select('title')->first();

            NotifyUserForProject::dispatch($agent->user_id, $agent, $projectContent->title);
        });
        Session::flash('success', __('Added successfully!'));

        return Response::json(['status' => 'success'], 200);
    }


    public function updateStatus(Request $request, $username)
    {
        $property = Project::findOrFail($request->projectId);

        if ($request->status == 1) {
            $property->update(['complete_status' => 1]);
        } else {
            $property->update(['complete_status' => 0]);
        }
        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }

    public function edit($username, $id)
    {
        $agent = Auth::guard('agent')->user();
        $tenantId = $agent->user_id;
        $project = Project::with(['galleryImages', 'floorplanImages'])->where('agent_id', $agent->id)->findOrFail($id);
        $information['project'] = $project;
        $information['gallery_images'] = $project->galleryImages;
        $information['floor_plan_images'] = $project->floorplanImages;
        $information['tenantLangs'] = $this->allLangs($agent->user_id);
        $information['language'] = $this->currentLang($agent->user_id);




        $language = $this->defaultLang($tenantId);
        $information['language'] = $language;

        $information['projectCategories'] = Category::where([['status', 1], ['user_id', $tenantId]])->with(['categoryContent' => function ($q) use ($language) {
            $q->where('language_id', $language->id);
        }])->get();
        $information['states'] = State::getStates($tenantId, $language->id);

        $information['projectCountries'] = Country::where('user_id', $tenantId)->get();
        $information['projectStates'] = State::where('user_id', $tenantId)->where('country_id', $project->country_id)->get();

        $information['projectCities'] = City::where('user_id', $tenantId)->where('state_id', $project->state_id)->get();

        $information['specifications'] = ProjectSpecification::where('project_id', $project->id)->get();

        return view('agent.project.edit', $information);
    }


    public function update(ProjectUpdateRequest $request, $username, $id)
    {
        DB::transaction(function () use ($request) {
            $agent = Auth::guard('agent')->user();
            $project = Project::where('agent_id', $agent->id)->findOrFail($request->project_id);

            $featuredImgName = $project->featured_image;

            if ($request->hasFile('featured_image')) {
                $featuredImgName = UploadFile::update('assets/img/project/featured/', $request->featured_image, $project->featured_image);
            }

            $project->update([
                'agent_id' =>  $agent->id,
                'user_id' =>  $agent->user_id,
                'category_id' => $request->category_id,
                'country_id' => $request->country_id ?? null,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id,
                'featured_image' => $featuredImgName,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'featured' => $project->featured,
                'complete_status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            $d_project_specifications = ProjectSpecification::where('project_id', $request->project_id)->get();
            foreach ($d_project_specifications as $d_project_specification) {
                $d_project_specification_contents = ProjectSpecificationContent::where('project_spacification_id', $d_project_specification->id)->get();
                foreach ($d_project_specification_contents as $d_project_specification_content) {
                    $d_project_specification_content->delete();
                }
                $d_project_specification->delete();
            }

            $languages = $this->allLangs($agent->user_id);
            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                    $projectContent =  ProjectContent::where('project_id', $request->project_id)->where('language_id', $language->id)->first();
                    if (empty($projectContent)) {
                        $projectContent = new ProjectContent();
                    }
                    $projectContent->language_id = $language->id;
                    $projectContent->project_id = $project->id;
                    $projectContent->title = $request[$language->code . '_title'];
                    $projectContent->slug = $request[$language->code . '_title'];

                    $projectContent->address = $request[$language->code . '_address'];
                    $projectContent->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
                    $projectContent->meta_keyword = $request[$language->code . '_meta_keyword'];
                    $projectContent->meta_description = $request[$language->code . '_meta_description'];
                    $projectContent->save();
                }
                $label_datas = $request[$language->code . '_label'];
                foreach ($label_datas as $key => $data) {
                    if (!empty($request[$language->code . '_value'][$key])) {
                        $project_specification = ProjectSpecification::where([['project_id', $project->id], ['key', $key]])->first();
                        if (is_null($project_specification)) {
                            $project_specification = new ProjectSpecification();
                            $project_specification->project_id = $project->id;
                            $project_specification->key  = $key;
                            $project_specification->user_id  = $agent->user_id;
                            $project_specification->save();
                        }
                        $project_specification_content = new ProjectSpecificationContent();
                        $project_specification_content->language_id = $language->id;
                        $project_specification_content->project_spacification_id = $project_specification->id;
                        $project_specification_content->label = $data;
                        // $project_specification_content->user_id  = $agent->user_id;
                        $project_specification_content->value = $request[$language->code . '_value'][$key];
                        $project_specification_content->save();
                    }
                }
            }
        });
        Session::flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function specificationDelete(Request $request, $username)
    {
        $tenantId = Auth::guard('agent')->user()->user_id;
        try {
            $d_project_specification = ProjectSpecification::where([['user_id', $tenantId], ['id', $request->spacificationId]])->first();

            $d_project_specification->deleteSpecification();


            return Response::json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return Response::json(['status' => 'error'], 400);
        }
    }


    public function destroy($username, Request $request)
    {
        try {
            $agentId = Auth::guard('agent')->user()->id;
            $project =  Project::where([['agent_id', $agentId], ['id', $request->project_id]])->first();
            if ($project) {
                $project->destroyProject();
            }
        } catch (\Exception $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }

        Session::flash('success', __('Deleted successfully!'));
        return redirect()->back();
    }


    public function bulkDestroy($username, Request $request)
    {
        $agentId = Auth::guard('agent')->user()->id;
        $propertyIds = $request->ids;
        try {
            foreach ($propertyIds as $id) {
                $project =  Project::where([['agent_id', $agentId], ['id', $id]])->first();
                if ($project) {
                    $project->destroyProject();
                }
            }
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return redirect()->back();
        }

        Session::flash('success', __('Deleted successfully!'));
        return response()->json(['status' => 'success'], 200);
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

        $messages = Contact::where('user_project_contacts.agent_id', $agent->id)
            ->leftJoin('user_projects', 'user_project_contacts.project_id', 'user_projects.id')
            ->leftJoin('user_project_contents', 'user_projects.id', 'user_project_contents.project_id')
            ->where('user_project_contents.language_id', $language->id)
            ->when($title, function ($query) use ($title) {
                return $query->where('user_project_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->select('user_project_contacts.*', 'user_project_contents.title', 'user_project_contents.slug')
            ->latest()->get();


        return view('agent.project.message', compact('messages'));
    }
    public function destroyMessage(Request $request, $username)
    {
        $message = Contact::where('agent_id', Auth::guard('agent')->user()->id)->find($request->message_id);
        if ($message) {
            $message->delete();
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('Something went wrong!'));
        }
        return redirect()->back();
    }
}
