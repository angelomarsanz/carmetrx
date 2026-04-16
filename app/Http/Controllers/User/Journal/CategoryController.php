<?php

namespace App\Http\Controllers\User\Journal;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogCategoryRequest;
use App\Models\User\Journal\BlogCategory;
use App\Models\User\Journal\BlogCategoryContent;
use App\Models\User\Journal\BlogInformation;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Exception;
use Illuminate\Http\Request;
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
        $information['tenantFrontLangs'] = $this->allLangs($tenantId);
        $information['categories'] = BlogCategory::getCategories($tenantId, $language->id);
        $information['tenantFrontLangs'] = $this->allLangs($tenantId);
        return view('user.journal.category.index', $information);
    }

    public function store(BlogCategoryRequest $request)
    {

        try {
            DB::beginTransaction();

            $tenantId =  Auth::guard('web')->user()->id;
            $languages = $this->allLangs($tenantId);
            $category = BlogCategory::create([
                'user_id' => $tenantId,
                'status' => $request['status'],
                'serial_number' => $request['serial_number'],
            ]);

            foreach ($languages as $lang) {
                BlogCategoryContent::create([
                    'user_id' => $tenantId,
                    'language_id' => $lang->id,
                    'category_id' => $category->id,
                    'name' => $request[$lang->code . '_name'],
                    'slug' => $request[$lang->code . '_name'],
                ]);
            }

            Session::flash('success', __('Added successfully!'));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Session::flash('warning', $e->getMessage());
        }




        return "success";
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


        $category = BlogCategory::findOrFail($request->id);


        $category->update([

            'status' => $request->status,
            'serial_number' => $request->serial_number
        ]);

        foreach ($languages as $lan) {

            $categoryContent = BlogCategoryContent::where([['category_id', $request->id], ['language_id', $lan->id]])->first();
            if (empty($categoryContent)) {
                $categoryContent  = new  BlogCategoryContent();
                $categoryContent->user_id = $tenantId;
                $categoryContent->category_id = $category->id;
                $categoryContent->language_id = $lan->id;
                $categoryContent->save();
            }

            $categoryContent->update([
                'name' => $request[$lan->code . '_name'],
                'slug' => $request[$lan->code . '_name'],
            ]);
        }


        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function destroy($id)
    {
        $category = BlogCategory::query()->where('user_id', Auth::guard('web')->user()->id)->findOrFail($id);
        if (BlogInformation::where('user_id', Auth::guard('web')->user()->id)->where('blog_category_id', $category->id)->count() > 0) {
            return redirect()->back()->with('warning', __('First delete all the blog related to this category!'));
        } else {
            $category->deleteCategory();
            return redirect()->back()->with('success', __('Deleted successfully!'));
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $category = BlogCategory::query()->where('user_id', Auth::guard('web')->user()->id)->findOrFail($id);
            if (BlogInformation::where('user_id', Auth::guard('web')->user()->id)->where('blog_category_id', $category->id)->count() > 0) {
                Session::flash('warning', __('First delete all the blog related to this categories!'));
                break;
            } else {
                $category->deleteCategory();
                Session::flash('success', __('Deleted successfully!'));
            }
        }
        return "success";
    }
}
