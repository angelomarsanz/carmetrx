<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Partner;
use App\Models\Language;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::query()->orderBy('serial_number', 'asc')->get();

        return view('admin.home.partner.index', compact('partners'));
    }

    public function store(Request $request)
    {
        $rules = [
            'image' => [
                'required',
                $request->hasFile('image') ? new ImageMimeTypeRule() : ''
            ],
            'language' => "required",
            'url' => 'required|url',
            'serial_number' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $imageName = UploadFile::store('assets/front/img/partners/', $request->file('image'));

        Partner::storePartner($request, $imageName);

        $request->session()->flash('success', __('Added successfully!'));

        return  'success';
    }
    public function edit($id)
    {
        $partner = Partner::find($id);
        return view('admin.home.partner.edit', compact('partner'));
    }
    public function update(Request $request)
    {

        $rules = [
            'image' => $request->hasFile('image') ? new ImageMimeTypeRule() : '',
            'url' => 'required|url',
            'serial_number' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }
        $partner = Partner::find($request->partner_id);

        if ($request->hasFile('image')) {
            $newImage = $request->file('image');
            $oldImage = $partner->image;
            $imageName = UploadFile::update('assets/front/img/partners/', $newImage, $oldImage);
        }
        $image = $request->hasFile('image') ? $imageName : $partner->image;
        $partner->updatePartner($request, $image);


        $request->session()->flash('success', __('Updated successfully!'));

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy(Request $request)
    {
        $partner = Partner::findOrFail($request->partner_id);

        @unlink('assets/front/img/partners/' . $partner->image);

        $partner->delete();

        $request->session()->flash('success', __('Deleted successfully!'));

        return redirect()->back();
    }
}
