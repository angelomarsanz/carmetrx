<?php

namespace App\Http\Controllers\User\HomePage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\User\BasicSetting;
use App\Models\User\HomePage\CounterInformation;
use App\Models\User\HomePage\HomePage;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Traits\UserLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CounterController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request)
    {
        $userId =  Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);
        $information['languages'] = $this->allLangs($userId);
        $information['data'] = $language->sectionTitle()->select('id', 'featured_property_section_title')->first();
        $information['themeVersion'] = BasicSetting::where('user_id', $userId)->pluck('theme_version')->first();
        $information['counters'] = $language->counterInfos()->orderByDesc('id')->get();
        
        return view('user.home-page.counter-section.index', $information);
    }
    
    public function store(Request $request)
    {
        $rules = [
            'icon' => 'required',
            'amount' => 'required|numeric',
            'title' => 'required'
        ];

       
        $validator = Validator::make($request->all(), $rules);
        $tenantId =  Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $info = CounterInformation::create([
            'user_id' => $tenantId,
            'language_id' => $language->id,
            'icon' => $request->icon,
            'amount' => $request->amount,
            'title' => $request->title
        ]);
        if ($info) {
            Session::flash('success', __('Added successfully!'));
        } else {
            Session::flash('warning', __('Something went wrong!'));
        }

        return response()->json('success');
    }
    public function update(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric',
            'title' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $counterInfo = CounterInformation::query()->find($request->id);

        $counterInfo->update([
            'icon' => $request->icon,
            'amount' => $request->amount,
            'title' => $request->title
        ]);

        Session::flash('success', __('Updated successfully!'));

        return Response::json('success');
    }
    public function destroy($id)
    {
        $counterInfo = CounterInformation::query()->find($id);

        $counterInfo->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request['ids'];

        foreach ($ids as $id) {
            $counterInfo = CounterInformation::find($id);

            $counterInfo->delete();
        }

        Session::flash('success', __('Deleted successfully!'));

        return Response::json('success');
    }
}
