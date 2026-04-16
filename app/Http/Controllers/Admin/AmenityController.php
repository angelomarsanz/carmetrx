<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Traits\AdminLanguage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User\Property\Amenity;
use App\Traits\Tenant\TenantLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User\Property\AmenityContent;

class AmenityController extends Controller
{
    use AdminLanguage;
    public function index(Request $request)
    {
        $name = request()->filled('name') ? request('name') : null;

        $amenities = Amenity::where('user_id', 0)
            ->when($name, function ($query, $name) {
                $query->whereHas('contents', function ($q) use ($name) {
                    $q->where('name', 'LIKE', "%{$name}%");
                });
            })
            ->orderBy('serial_number', 'asc')
            ->paginate(10);
        $information['amenities'] = $amenities;

        return view('admin.property-management.amenity.index', $information);
    }

    public function store(Request $request)
    {
        $rules = [
            'icon' => 'required',
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];



        $languages = $this->allLangs();
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] = ['required', Rule::unique('user_amenity_contents', 'name')->where('user_id', 0)];
            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' ' . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $amenity =  Amenity::create([
                'user_id' => 0,
                'status' => $request->status,
                'icon' => $request->icon,
                'serial_number' => $request->serial_number
            ]);
            foreach ($languages as $lang) {

                AmenityContent::create([
                    'user_id' => 0,
                    'language_id' => $lang->id,
                    'amenity_id' => $amenity->id,
                    'name' => $request[$lang->code . '_name'],
                    'slug' => $request[$lang->code . '_name'],
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

    public function update(Request $request)
    {
        $rules = [
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];

        $languages = $this->allLangs();
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] = ['required', Rule::unique('user_amenity_contents', 'name')->ignore($request->amenity_id, 'amenity_id')];


            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' ' . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        DB::beginTransaction();
        try {

            $amenity =  Amenity::find($request->amenity_id);
            $amenity->update([
                'status' => $request->status,
                'icon' => $request->icon,
                'serial_number' => $request->serial_number
            ]);

            foreach ($languages as $lan) {
                $aminityContent = AmenityContent::where([['language_id', $lan->id], ['amenity_id', $request->amenity_id]])->first();
                if (empty($aminityContent)) {
                    $aminityContent  = new  AmenityContent();
                    $aminityContent->user_id = 0;
                    $aminityContent->amenity_id = $amenity->id;
                    $aminityContent->language_id = $lan->id;
                    $aminityContent->save();
                }

                $aminityContent->update([
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
        Session::flash('success', __('Updated successfully!'));

        return 'success';
    }

    public function destroy(Request $request)
    {
        $amenity = Amenity::where([['id', $request->id]])->firstOrFail();
        $delete = $amenity->deleteAmenity();
        if ($delete) {
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('You can not delete this amenity! A property included in this amenity.'));
        }
        return redirect()->back();
    }


    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $amenity = Amenity::where([['id', $id]])->first();
            $delete = $amenity->deleteAmenity();

            if ($delete == false) {
                Session::flash('warning', __('You can not delete all amenity!! A property included in this amenity.'));
                return 'success';
            }
        }
        Session::flash('success', __('Deleted successfully!'));

        return 'success';
    }
}
