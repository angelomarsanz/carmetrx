<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectManagment\ProjectTypeRequest;
use App\Models\User\Project\Project;
use App\Models\User\Project\ProjectType;
use App\Models\User\Project\ProjectTypeContent;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TypeController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request, $username, $id)
    {

        $agent =  Auth::guard('agent')->user();
        $tenantId =  $agent->user_id;

        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }


        $data['tenantLangs'] = $this->allLangs($tenantId);
        $project = Project::where('agent_id', $agent->id)->findOrFail($id);
        $data['project_id'] = $project->id;

        $data['types'] = ProjectType::where([['user_project_types.project_id', $project->id], ['user_project_types.user_id', $tenantId]])
            ->join('user_project_type_contents', 'user_project_types.id', 'user_project_type_contents.project_type_id')
            ->where('user_project_type_contents.language_id', $language->id)
            ->select('user_project_types.*', 'user_project_type_contents.title', 'user_project_type_contents.min_price', 'user_project_type_contents.min_area', 'user_project_type_contents.max_area', 'user_project_type_contents.max_price', 'user_project_type_contents.unit')
            ->paginate(10);

        return view('agent.project.type.index', $data);
    }

    public function store(ProjectTypeRequest $request, $username)
    {
        $agent = Auth::guard('agent')->user();
        $languages = $this->allLangs($agent->user_id);


        DB::beginTransaction();
        try {
            $projectType = new ProjectType();
            $projectType->project_id = $request->project_id;
            $projectType->user_id = $agent->user_id;
            $projectType->save();



            foreach ($languages as $language) {
                $contentData = [
                    'language_id' => $language->id,
                    'title' => $request[$language->code . '_name'],
                    'unit' => $request[$language->code . '_total_unit'],
                    'min_area' => $request[$language->code . '_min_area'],
                    'max_area' => $request[$language->code . '_max_area'],
                    'min_price' => $request[$language->code . '_min_price'],
                    'max_price' => $request[$language->code . '_max_price'],
                ];
                if ($request->filled($language->code . '_name') || $request->filled($language->code . '_total_unit') || $request->filled($language->code . '_min_price') || $request->filled($language->code . '_min_area') || $request->filled($language->code . '_max_area') || $request->filled($language->code . '_max_price')) {
                    ProjectTypeContent::storeTypeContent($agent->user_id, $projectType->id, $contentData);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            Session::flash('warning', $e->getMessage());
            return Response::json(['status' => 'success'], 200);
        }
        Session::flash('success', __('Added successfully!'));

        return Response::json(['status' => 'success'], 200);
    }


    public function update(ProjectTypeRequest $request, $username)
    {
        $agent = Auth::guard('agent')->user();
        $languages = $this->allLangs($agent->user_id);
        foreach ($languages as $language) {

            if ($request->filled($language->code . '_name') || $request->filled($language->code . '_total_unit') || $request->filled($language->code . '_min_price') || $request->filled($language->code . '_min_area') || $request->filled($language->code . '_max_area') || $request->filled($language->code . '_max_price')) {

                $projectType =  ProjectTypeContent::where('project_type_id', $request->type_id)->where('language_id', $language->id)->first();

                if (empty($projectType)) {
                    $projectType = new ProjectTypeContent();
                    $projectType->project_type_id =  $request->type_id;
                    $projectType->language_id =  $language->id;
                    $projectType->user_id = $agent->user_id;
                }
                $projectType->title = $request[$language->code . '_name'];
                $projectType->unit = $request[$language->code . '_total_unit'];
                $projectType->min_area =  $request[$language->code . '_min_area'];
                $projectType->max_area =  $request[$language->code . '_max_area'];
                $projectType->min_price =  $request[$language->code . '_min_price'];
                $projectType->max_price =  $request[$language->code . '_max_price'];
                $projectType->save();
            }
        }

        Session::flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function delete(Request $request, $username)
    {

        try {
            $this->deleteType($request->id);
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return redirect()->back();
        }

        Session::flash('success', __('Deleted successfully!'));
        return redirect()->back();
    }

    protected function deleteType($id)
    {
        $type = ProjectType::find($id);
        $type->deleteType();
        return;
    }

    public function bulkDelete(Request $request, $username)
    {
        $propertyIds = $request->ids;
        try {
            foreach ($propertyIds as $id) {
                $this->deleteType($id);
            }
        } catch (\Exception $e) {
            Session::flash('warning', __('Something went wrong!'));

            return redirect()->back();
        }
        Session::flash('success', __('Deleted successfully!'));
        return response()->json(['status' => 'success'], 200);
    }
}
