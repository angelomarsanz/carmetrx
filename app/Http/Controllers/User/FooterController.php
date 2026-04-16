<?php

namespace App\Http\Controllers\User;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\User\BasicSetting;
use App\Models\User\FooterQuickLink;
use App\Models\User\FooterText;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FooterController extends Controller
{
    use TenantFrontendLanguage;
    public function logo()
    {
        $data = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('footer_logo', 'footer_bg_img')->first();

        return view('user.footer.logo', compact('data'));
    }
    public function updateLogo(Request $request)
    {
        $data =  BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('id', 'footer_logo')->first();

        $rules = [];

        if (is_null($data->footer_logo)) {
            $rules['footer_logo'] = 'required';
        }
        if ($request->hasFile('footer_logo')) {
            $rules['footer_logo'] = new ImageMimeTypeRule();
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if ($request->hasFile('footer_logo')) {
            $newLogo = $request->file('footer_logo');
            $oldLogo = $data->footer_logo;
            $logoName = UploadFile::update(Constant::WEBSITE_FOOTER_LOGO . '/', $newLogo, $oldLogo);

            // finally, store the footer-logo into db
            $data->update(
                ['footer_logo' => $logoName]
            );

            Session::flash('success', __('Updated successfully!'));
        }

        return redirect()->back();
    }
    public function updateBgImage(Request $request)
    {
        $data =  BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('id', 'footer_bg_img')->first();

        $rules = [];

        if (is_null($data->footer_logo)) {
            $rules['footer_background_image'] = 'required';
        }
        if ($request->hasFile('footer_background_image')) {
            $rules['footer_background_image'] = new ImageMimeTypeRule();
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if ($request->hasFile('footer_background_image')) {
            $newLogo = $request->file('footer_background_image');
            $oldLogo = $data->footer_bg_img;
            $logoName = UploadFile::update(Constant::WEBSITE_FOOTER_LOGO . '/', $newLogo, $oldLogo);

            // finally, store the footer-logo into db
            $data->update(
                ['footer_bg_img' => $logoName]
            );

            Session::flash('success', __('Updated successfully!'));
        }

        return redirect()->back();
    }

    public function footerContent(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        $information['themeInfo'] = BasicSetting::where('user_id', $tenantId)
            ->select('theme_version')
            ->first();

        $information['data'] = FooterText::where('language_id', $language->id)
            ->where('user_id', $tenantId)
            ->first();
        return view('user.footer.text', $information);
    }

    public function updateFooterContent(Request $request)
    {


        $rules = [
            'about_company' => 'sometimes|required',
            'copyright_text' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $tenantId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        FooterText::query()->updateOrInsert(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'language_id' => $language->id,
            ],
            [
                'copyright_text' => clean($request->copyright_text),
                'about_company' => clean($request->about_company),
                'user_id' => $tenantId
            ]
        );
        Session::flash('success', __('Updated successfully!'));
        return 'success';
    }


    public function quickLinks(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        $information['links'] = FooterQuickLink::where([['language_id', $language->id], ['user_id', $tenantId]])->orderBy('serial_number', 'asc')->get();

        return view('user.footer.quick_links', $information);
    }

    public function storeQuickLink(Request $request)
    {
        $rules = [
            'title' => 'required',
            'url' => 'required',
            'serial_number' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $tenantId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        FooterQuickLink::create([
            'language_id' => $language->id,
            'user_id' => $tenantId,
            'title' => $request->title,
            'url' => $request->url,
            'serial_number' => $request->serial_number,
        ]);
        Session::flash('success', __('Added successfully!'));
        return 'success';
    }

    public function updateQuickLink(Request $request)
    {
        $rules = [
            'title' => 'required',
            'url' => 'required',
            'serial_number' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        FooterQuickLink::where('user_id', Auth::guard('web')->user()->id)
            ->where('id', $request->link_id)
            ->firstOrFail()
            ->update([
                'title' => $request->title,
                'url' => $request->url,
                'serial_number' => $request->serial_number,
            ]);
        Session::flash('success', __('Updated successfully!'));
        return 'success';
    }

    public function deleteQuickLink(Request $request)
    {
        FooterQuickLink::find($request->link_id)->delete();
        Session::flash('success', __('Deleted successfully!'));
        return redirect()->back();
    }
}
