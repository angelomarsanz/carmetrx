<?php

namespace App\Http\Controllers\User\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\ProjectManagment\ProjectStoreRequest;
use App\Http\Requests\ProjectManagment\ProjectUpdateRequest;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
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

class ProjectController extends Controller
{
    use TenantFrontendLanguage;
    public function settings()
    {
        $content = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('project_state_status', 'project_country_status')->first();
        return view('user.project-management.settings', compact('content'));
    }
    //update_setting
    public function updateSettings(Request $request)
    {
        $status = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->first();

        $status->project_state_status = $request->project_state_status;
        $status->project_country_status = $request->project_country_status;
        $status->save();
        Session::flash('success', __('Updated successfully!'));
        return back();
    }

    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $data['langs'] = $this->allLangs($tenantId);

        if ($request->has('language')) {

            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }

        // $data['language'] = $language;
        $language_id = $language->id;

        $title = null;

        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }
        $data['projects'] = Project::where('user_projects.user_id', $tenantId)
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

        return view('user.project-management.index', $data);
    }

    public function create(Request $request)
    {
        $information = [];
        $tenantId = Auth::guard('web')->user()->id;

        $information['projectCategories'] = Category::where('user_id', $tenantId)->where('status', 1)->get();
        $information['projectCountries'] = Country::where('user_id', $tenantId)->get();

        $information['projectSettings'] = BasicSetting::where('user_id', $tenantId)->select('project_state_status', 'project_country_status')->first();
        $information['states'] = State::where('user_id', $tenantId)->get();
        $information['cities'] = City::where('user_id', $tenantId)->where('status', 1)->get();

        $information['tenantFrontLangs'] =  $this->allLangs($tenantId);
        $information['language'] = $this->defaultLang($tenantId);
        $information['agents'] = Agent::where([['user_id', $tenantId], ['status', 1]])->get();
        return view('user.project-management.create', $information);
    }

    public function galleryImagesStore(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
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
        $imageName = UploadFile::store('assets/img/project/gallery-images/', $request->file('file'));

        $pi = new ProjectGalleryImage();
        if (!empty($request->project_id)) {
            $pi->project_id = $request->project_id;
        }
        $pi->user_id = $userId;
        $pi->image = $imageName;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }

    public function galleryImageRmv(Request $request)
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
    public function galleryImageDbrmv(Request $request)
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


    public function floorPlanImagesStore(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
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

        $pi = new ProjectFloorplanImage();
        if (!empty($request->project_id)) {
            $pi->project_id = $request->project_id;
        }
        $pi->user_id = $userId;
        $pi->image = $imageName;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }
    public function floorPlanImageRmv(Request $request)
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
    public function floorPlanImageDbrmv(Request $request)
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


    public function store(ProjectStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $tenantId = Auth::guard('web')->user()->id;

            $featuredImgURL = $request->featured_image;
            if (request()->hasFile('featured_image')) {
                $featuredImgName = UploadFile::store('assets/img/project/featured', $featuredImgURL);
            }

            $languages = $this->allLangs($tenantId);
            $requestData = $request->all();
            $requestData['featuredImgName'] = $featuredImgName;
            $project = Project::storeProject($tenantId, $requestData);


            $gallery_images = $request->gallery_images;
            if ($gallery_images) {
                $pis = ProjectGalleryImage::where('user_id', $tenantId)->findOrFail($gallery_images);
                foreach ($pis as $key => $pi) {
                    $pi->project_id = $project->id;
                    $pi->user_id = $tenantId;
                    $pi->save();
                }
            }

            $floor_plan_images = $request->floor_plan_images;
            if ($floor_plan_images) {
                $pis = ProjectFloorplanImage::where('user_id', $tenantId)->findOrFail($floor_plan_images);
                foreach ($pis as $key => $pi) {
                    $pi->project_id = $project->id;
                    $pi->user_id = $tenantId;
                    $pi->save();
                }
            }

            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                    $requstData = [
                        'project_id' => $project->id,
                        'language_id' => $language->id,
                        'title' => $request[$language->code . '_title'],
                        'address' => $request[$language->code . '_address'],
                        'description' => $request[$language->code . '_description'],
                        'meta_keyword' => $request[$language->code . '_meta_keyword'],
                        'meta_description' => $request[$language->code . '_meta_description'],
                    ];
                    ProjectContent::storeProjectContent($requstData);
                }

                $label_datas = $request[$language->code . '_label'];
                foreach ($label_datas as $key => $data) {
                    if (!empty($request[$language->code . '_value'][$key])) {
                        $project_specification = ProjectSpecification::where([['project_id', $project->id], ['key', $key]])->first();
                        if (is_null($project_specification)) {
                            $project_specification = ProjectSpecification::storeSpecification($tenantId, $project->id, $key);
                        }
                        $sepConData = [
                            'language_id' => $language->id,
                            'label' => $data,
                            'value' => $request[$language->code . '_value'][$key],
                        ];
                        ProjectSpecificationContent::storeSpecificationContent($project_specification->id, $sepConData);
                    }
                }
            }
        });
        Session::flash('success', __('Added successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function updateFeatured(Request $request)
    {
        $property = Project::findOrFail($request->projectId);

        if ($request->featured == 1) {
            $property->update(['featured' => 1]);
        } else {
            $property->update(['featured' => 0]);
        }
        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }

    public function updateStatus(Request $request)
    {
        $project = Project::findOrFail($request->projectId);
        $project->update(['complete_status' => $request->status]);

        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }



    public function edit($id)
    {
        $tenantID = Auth::guard('web')->user()->id;
        $project = Project::where('user_id', $tenantID)->findOrFail($id);
        $information['project'] = $project;
        $information['projectContents'] = ProjectContent::where('project_id', $project->id)->get();
        $information['gallery_images'] = $project->galleryImages;
        $information['floor_plan_images'] = $project->floorplanImages;
        $information['language'] = $this->defaultLang($tenantID);
        $information['tenantFrontLangs'] = $this->allLangs($tenantID);
        $information['agents'] = Agent::where([['user_id', $tenantID], ['status', 1]])->get();
        $information['specifications'] = ProjectSpecification::where('user_id', $tenantID)->where('project_id', $project->id)->get();

        $information['projectCategories'] = Category::where([['user_id', $tenantID], ['status', 1]])->get();
        $information['projectCountries'] = Country::where('user_id', $tenantID)->get();
        $information['projectStates'] = State::where('user_id', $tenantID)->where('country_id', $project->country_id)->get();
        $information['projectCities'] = City::where('user_id', $tenantID)->where('state_id', $project->state_id)->get();
        $information['states'] = State::getStates($tenantID, $information['language']->id);
        $information['projectSettings'] = BasicSetting::where('user_id', $tenantID)->select('project_state_status', 'project_country_status')->first();

        return view('user.project-management.edit', $information);
    }


    public function update(ProjectUpdateRequest $request, $id)
    {

        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        $project = Project::where('user_id', $tenantId)->findOrFail($request->project_id);


        $featuredImgName = $project->featured_image;


        if ($request->hasFile('featured_image')) {
            $featuredImgName = UploadFile::update('assets/img/project/featured/', $request->featured_image, $project->featured_image);
        }
        $requestData = $request->all();
        $requestData['featuredImgName'] = $featuredImgName;
        $requestData['featured'] = $project->featured;
        $project->updateProject($requestData);

        $d_project_specifications = ProjectSpecification::where('project_id', $request->project_id)->get();
        foreach ($d_project_specifications as $d_project_specification) {
            $d_project_specification_contents = ProjectSpecificationContent::where('project_spacification_id', $d_project_specification->id)->get();
            foreach ($d_project_specification_contents as $d_project_specification_content) {
                $d_project_specification_content->delete();
            }
            $d_project_specification->delete();
        }

        foreach ($languages as $language) {
            if ($request->filled($language->code . '_title') || $request->filled($language->code . '_address') || $request->filled($language->code . '_description') || $request->filled($language->code . '_meta_keyword') || $request->filled($language->code . '_meta_description')) {
                $contentData  = [
                    'language_id' => $language->id,
                    'title' => $request[$language->code . '_title'],
                    'slug' => $request[$language->code . '_title'],
                    'address' => $request[$language->code . '_address'],
                    'description' => $request[$language->code . '_description'],
                    'meta_keyword' => $request[$language->code . '_meta_keyword'],
                    'meta_description' => $request[$language->code . '_meta_description'],
                ];
                ProjectContent::updateOrCreateProjectContent($project->id, $contentData);
            }

            $label_datas = $request[$language->code . '_label'];
            foreach ($label_datas as $key => $data) {
                if (!empty($request[$language->code . '_value'][$key])) {
                    $project_specification = ProjectSpecification::where([['project_id', $project->id], ['key', $key]])->first();
                    if (is_null($project_specification)) {
                        $project_specification = ProjectSpecification::storeSpecification($tenantId, $project->id, $key);
                    }
                    $sepConData = [
                        'language_id' => $language->id,
                        'label' => $data,
                        'value' => $request[$language->code . '_value'][$key],
                    ];
                    ProjectSpecificationContent::storeSpecificationContent($project_specification->id, $sepConData);
                }
            }
        }

        Session::flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }
    public function specificationDelete(Request $request)
    {
        $d_project_specification = ProjectSpecification::find($request->spacificationId);

        $d_project_specification_contents = ProjectSpecificationContent::where('project_spacification_id', $d_project_specification->id)->get();
        foreach ($d_project_specification_contents as $d_project_specification_content) {
            $d_project_specification_content->delete();
        }
        $d_project_specification->delete();

        return Response::json(['status' => 'success'], 200);
    }


    public function destroy(Request $request)
    {
        try {
            $tenantId = Auth::guard('web')->user()->id;
            $project =  Project::where([['user_id', $tenantId], ['id', $request->project_id]])->firstOrFail();
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


    public function bulkDestroy(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $propertyIds = $request->ids;
        try {
            foreach ($propertyIds as $id) {
                $project =  Project::where([['user_id', $tenantId], ['id', $id]])->firstOrFail();
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

    public function messages(Request $request)
    {
        $tanant = Auth::guard('web')->user();
        if ($request->has('language')) {
            $language = $this->selectLang($tanant->id, $request->language);
        } else {
            $language = $this->defaultLang($tanant->id);
        }

        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }
        $messages = Contact::where('user_project_contacts.user_id', $tanant->id)
            ->leftJoin('user_projects', 'user_project_contacts.project_id', 'user_projects.id')
            ->leftJoin('user_project_contents', 'user_projects.id', 'user_project_contents.project_id')
            ->where('user_project_contents.language_id', $language->id)
            ->when($title, function ($query) use ($title) {
                return $query->where('user_project_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->leftJoin('user_agents', 'user_project_contacts.agent_id', '=', 'user_agents.id')
            ->select('user_project_contacts.*', 'user_project_contents.title', 'user_project_contents.slug', 'user_agents.username')
            ->latest()->paginate(15);

        return view('user.project-management.message', compact('messages'));
    }


    public function messageDestroy(Request $request)
    {
        $message = Contact::where('user_id', Auth::guard('web')->user()->id)->find($request->message_id);
        if ($message) {
            $message->delete();
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('Something went wrong!'));
        }
        return redirect()->back();
    }
}
