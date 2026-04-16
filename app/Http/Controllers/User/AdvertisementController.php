<?php

namespace App\Http\Controllers\User;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Uploader;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\Advertisement\StoreRequest;
use App\Http\Requests\Advertisement\UpdateRequest;
use App\Models\User\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdvertisementController extends Controller
{
    public function index()
    {
        $ads = Advertisement::query()->where('user_id', Auth::guard('web')->user()->id)->orderByDesc('id')->get();
        return view('user.advertisement.index', compact('ads'));
    }

    public function store(StoreRequest $request): string
    {
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = UploadFile::store(Constant::WEBSITE_ADVERTISEMENT_IMAGE, $request->file('image'));
        }
        Advertisement::create($request->except('image') + [
            'user_id' => Auth::guard('web')->user()->id,
            'image' => $request->hasFile('image') ? $imageName : null
        ]);

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function update(UpdateRequest $request): string
    {
        $ad = Advertisement::query()->where('user_id', Auth::guard('web')->user()->id)->find($request->id);
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = UploadFile::update(Constant::WEBSITE_ADVERTISEMENT_IMAGE, $request->file('image'), $ad->image);
        }
        if ($request->ad_type == 'adsense') {
            // if ad type change to google adsense then delete the image from local storage.
            @unlink(public_path(Constant::WEBSITE_ADVERTISEMENT_IMAGE . '/' . $ad->image));
        }
        $ad->update($request->except('image') + [
            'image' => $request->hasFile('image') ? $imageName : $ad->image
        ]);
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $ad = Advertisement::query()->where('user_id', Auth::guard('web')->user()->id)->find($id);
        if ($ad->ad_type == 'banner') @unlink(public_path(Constant::WEBSITE_ADVERTISEMENT_IMAGE . '/' . $ad->image));
        $ad->delete();
        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function bulkDestroy(Request $request): string
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $ad = Advertisement::query()->where('user_id', Auth::guard('web')->user()->id)->find($id);
            if ($ad->ad_type == 'banner') @unlink(public_path(Constant::WEBSITE_ADVERTISEMENT_IMAGE . '/' . $ad->image));
            $ad->delete();
        }
        Session::flash('success', __('Deleted successfully!'));
        return "success";
    }
}
