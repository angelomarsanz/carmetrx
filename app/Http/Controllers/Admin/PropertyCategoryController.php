<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User\Language;
use Illuminate\Validation\Rule;
use App\Http\Helpers\UploadFile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\Tenant\TenantLanguage;
use App\Models\User\Property\Category;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User\Property\CategoryContent;

class PropertyCategoryController extends Controller
{
    use TenantLanguage;
    public function index(Request $request)
    {
        $language = Language::where('code', $request->language)->firstOrFail();

        $name = request()->filled('name') ? request('name') : null;

        $categories = Category::where('user_id', 0)
            ->when($name, function ($query, $name) {
                $query->whereHas('categoryContents', function ($q) use ($name) {
                    $q->where('name', 'LIKE', "%{$name}%");
                });
            })
            ->orderBy('serial_number', 'asc')
            ->paginate(10);
        $information['categories'] = $categories;

        return view('admin.property-management.category.index', $information);
    }

    public function store(Request $request)
    {
        $img = $request->file('image');


        $rules = [
            'type' => "required",
            'image' => "required",
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];

        $message = [
            'language_id.required' => __('The language field is required.')
        ];

        $languages = $this->allLangs();
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] = ['required', Rule::unique('user_property_category_contents', 'name')];
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
            $filename = UploadFile::store('assets/img/property-category/', $img);
        }

        DB::beginTransaction();

        try {
            $category = Category::create([
                'user_id' => 0,
                'type' => $request->type,
                'image' => $filename,
                'status' => $request->status,
                'serial_number' => $request->serial_number
            ]);
            foreach ($languages as $lan) {

                CategoryContent::create([
                    'user_id' => 0,
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
        $category = Category::findOrFail($request->categoryId);

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

        $languages = $this->allLangs();
        $message = [];
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_property_category_contents', 'name')->ignore($request->id, 'category_id')

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
            $filename = UploadFile::update('assets/img/property-category/', $img, $category->image);
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
                $categoryContent->user_id = 0;
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

        $category = Category::where([['user_id', 0], ['id', $request->id]])->firstOrFail();
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
        foreach ($ids as $id) {

            $category = Category::where([['user_id', 0], ['id', $id]])->first();

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
