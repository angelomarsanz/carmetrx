<?php

namespace App\Http\Controllers\User\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\User\Project\Category;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use App\Http\Helpers\UploadFile;
use App\Models\User\Project\CategoryContent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
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

        $categories = Category::getCategories($tenantId, $language->id, $name);

        $information['tenantFrontLangs'] = $this->allLangs($tenantId);
        $information['categories'] = collectionToPaginate($categories, 10);
        return view('user.project-management.category.index', $information);
    }

    public function store(Request $request)
    {
        $img = $request->file('image');


        $rules = [
            // 'type' => "required",
            'image' => "required",
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];

        $message = [
            'language_id.required' => __('The language field is required.')
        ];
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] = ['required', Rule::unique('user_project_category_contents', 'name')->where('user_id', $tenantId)];
            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' ' . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }
        $validator = Validator::make($request->all(), $rules, $message);


        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        if ($request->hasFile('image')) {
            $filename = UploadFile::store('assets/img/project-category/', $img);
        }

        DB::beginTransaction();

        try {
            $category = Category::create([
                'user_id' => $tenantId,
                'image' => $filename,
                'status' => $request->status,
                'serial_number' => $request->serial_number
            ]);
            foreach ($languages as $lan) {

                CategoryContent::create([
                    'user_id' => $tenantId,
                    'language_id' => $lan->id,
                    'category_id' => $category->id,
                    'name' => $request[$lan->code . '_name'],
                    'slug' => $request[$lan->code . '_name'],
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
    public function updateFeatured(Request $request)
    {
        $category = Category::where('user_id', Auth::guard('web')->user()->id)->findOrFail($request->categoryId);

        if ($request->featured == 1) {
            $category->update(['featured' => 1]);
        } else {
            $category->update(['featured' => 0]);
        }

        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }
    public function update(Request $request)
    {
        $rules = [

            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        $message = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_property_category_contents', 'name')->ignore($request->id, 'category_id')->where('user_id', $tenantId)

                ];

            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' ' . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $message);



        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $category = Category::find($request->id);

        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $filename = UploadFile::update('assets/img/project-category/', $img, $category->image);
        } else {
            $filename = $category->image;
        }
        $category->update([

            'image' => $filename,
            'status' => $request->status,
            'serial_number' => $request->serial_number
        ]);

        foreach ($languages as $lan) {

            $categoryContent = CategoryContent::where([['category_id', $request->id], ['language_id', $lan->id]])->first();
            if (empty($categoryContent)) {
                $categoryContent  = new  CategoryContent();
                $categoryContent->user_id = $tenantId;
                $categoryContent->category_id = $request->id;
                $categoryContent->language_id = $lan->id;
                $categoryContent->save();
            }

            $categoryContent->update([
                'name' => $request[$lan->code . '_name'],
                'slug' => $request[$lan->code . '_name'],
            ]);
        }

        Session::flash('success', __('Updated successfully!'));

        return 'success';
    }

    public function destroy(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $category = Category::where([['user_id', $tenantId], ['id', $request->id]])->firstOrFail();
        $delete = $category->deleteCategory();

        if ($delete) {
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('You can not delete this category! A property included in this category.'));
        }
        return redirect()->back();
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        $tenantId = Auth::guard('web')->user()->id;

        foreach ($ids as $id) {

            $category = Category::where([['user_id', $tenantId], ['id', $id]])->first();

            $delete = $category->deleteCategory();
            if ($delete == false) {
                Session::flash('warning', __('You cannot delete all categories! A property is included in this category.'));
                return 'success';
            }
        }

        Session::flash('success', __('Deleted successfully!'));

        return 'success';
    }
}
