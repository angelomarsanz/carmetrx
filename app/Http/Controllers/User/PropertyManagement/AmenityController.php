<?php

namespace App\Http\Controllers\User\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\User\Language;
use App\Models\User\Property\Amenity;
use App\Models\User\Property\AmenityContent;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AmenityController extends Controller
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
        // then, get the equipment categories of that language from db
        $amenities = Amenity::where('user_id', $tenantId)
            ->when($name, function ($query, $name) {
                $query->whereHas('contents', function ($q) use ($name) {
                    $q->where('name', 'LIKE', "%{$name}%");
                });
            })
            ->orderBy('serial_number', 'asc')->get()->map(
                function ($item) use ($language) {
                    $content = $item->getContent($language->id);
                    $item->name = optional($content)->name;
                    return $item;
                }
            )->filter(function ($item) {
                return $item->name !== null;
            });

        $information['amenities'] = collectionToPaginate($amenities, 10);
        $information['tenantFrontLangs'] = $this->allLangs($tenantId);
        return view('user.property-management.amenity.index', $information);
    }

    public function store(Request $request)
    {
        $rules = [
            'icon' => 'required',
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];



        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] = ['required', Rule::unique('user_amenity_contents', 'name')->where('user_id', $tenantId)];
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
                'user_id' => $tenantId,
                'status' => $request->status,
                'icon' => $request->icon,
                'serial_number' => $request->serial_number
            ]);
            foreach ($languages as $lang) {

                AmenityContent::create([
                    'user_id' => $tenantId,
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

        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] = ['required', Rule::unique('user_amenity_contents', 'name')->ignore($request->amenity_id, 'amenity_id')->where('user_id', $tenantId)];


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
                    $aminityContent->user_id = $tenantId;
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
        $tenantId = Auth::guard('web')->user()->id;
        $amenity = Amenity::where([['user_id', $tenantId], ['id', $request->id]])->firstOrFail();
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
        $tenantId = Auth::guard('web')->user()->id;
        foreach ($ids as $id) {
            $amenity = Amenity::where([['user_id', $tenantId], ['id', $id]])->first();
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
