<?php

namespace App\Http\Controllers\User;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Uploader;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\Popup\StoreRequest;
use App\Http\Requests\Popup\UpdateRequest;
use App\Models\User\Popup;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PopupController extends Controller
{
    use TenantFrontendLanguage;
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        $information['popups'] = Popup::where('language_id', $language->id)
            ->orderBy('id', 'desc')
            ->orderBy('serial_number', 'asc')
            ->get();
        return view('user.popup.index', $information);
    }

    /**
     * Show the popup type page to select one of them.
     *
     * @return
     */
    public function popupType()
    {

        return view('user.popup.popup-type');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return
     */
    public function create($type)
    {
        $popup = [1, 2, 3, 4, 5, 6, 7];
        if (in_array($type, $popup)) {
            $information['popupType'] = $type;
            return view('user.popup.create', $information);
        } {
            return abort(404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return string
     */
    public function store(StoreRequest $request)
    {
        $tenantId =  Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        $imageName = UploadFile::store(Constant::WEBSITE_ANNOUNCEMENT_POPUP_IMAGE, $request->file('image'));
        Popup::create($request->except('image', 'end_date', 'end_time', 'user_id') + [
            'image' => $imageName,
            'user_id' => $tenantId,
            'language_id' => $language->id,
            'end_date' => $request->has('end_date') ? Carbon::parse($request['end_date']) : null,
            'end_time' => $request->has('end_time') ? date('h:i', strtotime($request['end_time'])) : null
        ]);
        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    /**
     * Update the status of specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return
     */
    public function updateStatus(Request $request, int $id)
    {
        $tenantId =  Auth::guard('web')->user()->id;
        $popup = Popup::query()->where('user_id', $tenantId)->find($id);
        if ($request->status == 1) {
            $popup->update(['status' => 1]);
        } else {
            $popup->update(['status' => 0]);
        }
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return
     */
    public function edit($id)
    {

        $popup = Popup::findOrFail($id);
        return view('user.popup.edit', compact('popup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return string
     */
    public function update(UpdateRequest $request, $id)
    {
        $tenantId =  Auth::guard('web')->user()->id;
        $popup = Popup::query()->where('user_id', $tenantId)->findOrFail($id);
        if ($request->hasFile('image')) {
            $imageName = UploadFile::update(Constant::WEBSITE_ANNOUNCEMENT_POPUP_IMAGE, $request->file('image'), $popup->image);
        }
        $popup->update($request->except('image', 'end_date', 'end_time') + [
            'image' => $request->hasFile('image') ? $imageName : $popup->image,
            'end_date' => $request->has('end_date') ? Carbon::parse($request['end_date']) : null,
            'end_time' => $request->has('end_time') ? date('h:i', strtotime($request['end_time'])) : null
        ]);
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return
     */
    public function destroy($id)
    {
        $tenantId =  Auth::guard('web')->user()->id;
        $popup = Popup::query()->where('user_id', $tenantId)->find($id);
        @unlink(public_path(Constant::WEBSITE_ANNOUNCEMENT_POPUP_IMAGE . '/' . $popup->image));
        $popup->delete();
        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    /**
     * Remove the selected or all resources from storage.
     *
     * @param Request $request
     * @return string
     */
    public function bulkDestroy(Request $request)
    {
        $tenantId =  Auth::guard('web')->user()->id;
        $ids = $request->ids;
        foreach ($ids as $id) {
            $popup = Popup::query()->where('user_id', $tenantId)->find($id);
            @unlink(public_path(Constant::WEBSITE_ANNOUNCEMENT_POPUP_IMAGE . '/' . $popup->image));
            $popup->delete();
        }
        Session::flash('success', __('Deleted successfully!'));
        return "success";
    }
}
