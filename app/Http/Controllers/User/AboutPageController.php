<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\BasicSetting;
use Illuminate\Http\Request;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AboutPageController extends Controller
{
    use TenantFrontendLanguage;
    public function sections(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        if ($request->has('language')) {

            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }

        $data['ubs'] = BasicSetting::where('user_id', $tenantId)->firstOrFail();

        if (! is_null($data['ubs']->about_additional_section_status) && $data['ubs']->about_additional_section_status != "null") {
            $data['additional_section_statuses'] = json_decode($data['ubs']->about_additional_section_status, true);
        } else {
            $data['additional_section_statuses'] = [];
        }
        $data['langid'] =  $language->id;

        return view('user.about-page.sections', $data);
    }

    public function updatesections(Request $request)
    {

        $bs = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->firstOrFail();
        $bs->about_info_section              = $request->about_info_section;
        $bs->why_choose_us_section          = $request->why_choose_us_section;
        $bs->work_steps_section           = $request->work_steps_section;
        $bs->testimonial_section       = $request->testimonial_section;
        $bs->about_additional_section_status = json_encode($request->additional_sections, true);
        $bs->save();

       Session::flash('success', __('Updated Successfully'));
        return back();
    }
}
