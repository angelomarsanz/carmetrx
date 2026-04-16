<?php

namespace App\Http\Controllers\User\Homepage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Rules\ImageMimeTypeRule;
use App\Models\User\HomePage\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function index()
    {

        $partners = Partner::where('user_id', Auth::guard('web')->user()->id)->orderBy('serial_number', 'asc')->get();
        return view('user.home-page.partners.index', compact('partners'));
    }

    public function store(Request $request)
    {
        $rules = [
            'image' => [
                'required',
                $request->hasFile('image') ? new ImageMimeTypeRule() : ''
            ],
            'url' => 'required|url',
            'serial_number' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $imageName = UploadFile::store(Constant::WEBSITE_PARTNERS_IMAGE . '/', $request->file('image'));

        Partner::query()->create($request->except('image') + [
            'image' => $imageName,
            'user_id' => Auth::guard('web')->user()->id
        ]);

        session()->flash('success', __('Added successfully!'));

        return 'success';
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

        $partner = Partner::query()->findOrFail($request->id);

        if ($request->hasFile('image')) {
            $newImage = $request->file('image');
            $oldImage = $partner->image;
            $imageName = UploadFile::update(Constant::WEBSITE_PARTNERS_IMAGE . '/', $newImage, $oldImage);
        }

        $partner->update($request->except('image') + [
            'image' => $request->hasFile('image') ? $imageName : $partner->image
        ]);

        session()->flash('success', __('Updated successfully!'));

        return 'success';
    }

    public function destroy(Request $request, $id)
    {
        $partner = Partner::query()->findOrFail($id);

        @unlink(public_path(Constant::WEBSITE_PARTNERS_IMAGE . '/' . $partner->image));

        $partner->delete();

        session()->flash('success', __('Deleted successfully!'));

        return redirect()->back();
    }
}
