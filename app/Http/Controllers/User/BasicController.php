<?php

namespace App\Http\Controllers\User;

use App\Models\User\SEO;
use App\Constants\Constant;
use Illuminate\Http\Request;
use App\Models\User\Language;
use App\Http\Helpers\Uploader;
use App\Http\Helpers\UploadFile;
use App\Rules\ImageMimeTypeRule;
use App\Models\User\BasicSetting;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Models\User\SmtpInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class BasicController extends Controller
{
    public function breadcrumb(Request $request)
    {
        $data['basic_setting'] = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('breadcrumb')->first();
        return view('user.settings.breadcrumb', $data);
    }

    public function updateBreadcrumb(Request $request)
    {
        $bss = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('id', 'breadcrumb')->first();

        $rules = [];
        $rules['breadcrumb'] = 'required|mimes:jpg, jpeg, png, svg, gif';

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json(['errors' => $validator->errors(), 'id' => 'breadcrumb']);
        }

        if ($request->hasFile('breadcrumb')) {
            $filename = UploadFile::update(Constant::WEBSITE_BREADCRUMB, $request->file('breadcrumb'), $bss->breadcrumb);
            @unlink(public_path(Constant::WEBSITE_BREADCRUMB . '/' . $bss->breadcrumb));
            $bss->update(
                ['breadcrumb' => $filename]
            );
        }
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function footerLogo(Request $request)
    {

        $data['basic_setting'] = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('footer_logo')->first();
        return view('user.settings.footer-logo', $data);
    }

    public function updateFooterLogo(Request $request)
    {
        $bss = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('id', 'footer_logo')->first();

        $rules = [];
        if (!$request->filled('footer_logo') && is_null($bss->footer_logo)) {
            $rules['footer_logo'] = 'required';
        }
        if ($request->hasFile('footer_logo')) {
            $rules['logo'] = new ImageMimeTypeRule();
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json(['errors' => $validator->errors(), 'id' => 'footer_logo']);
        }

        if ($request->hasFile('footer_logo')) {

            $filename = UploadFile::update(Constant::WEBSITE_FOOTER_LOGO, $request->file('footer_logo'), $bss->footer_logo);
            @unlink(public_path(Constant::WEBSITE_FOOTER_LOGO . '/' . $bss->footer_logo));
            $bss->update(
                ['footer_logo' => $filename]
            );
        }
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function seo(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = Language::where('user_id', $userId)->where('code', $request->language)->first();
        } else {
            $language = Language::query()->where('is_default', 1)->where('user_id', $userId)->first();
        }

        $seo = SEO::where([['language_id', $language->id], ['user_id', $userId]]);
        if ($seo->count() == 0) {
            // if seo info of that language does not exist then create a new one
            SEO::create([
                'language_id' => $language->id,

                'user_id' => Auth::guard('web')->user()->id
            ]);
        }
        $information['language'] = $language;
        $information['data'] = $seo->first();

        return view('user.settings.seo', $information);
    }

    public function updateSEO(Request $request)
    {

        $userId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = Language::where('user_id', $userId)->where('code', $request->language)->first();
        } else {

            $language = Language::query()->where('is_default', 1)->where('user_id', $userId)->first();
        }

        SEO::where([['language_id', $language->id], ['user_id', $userId]])
            ->update($request->except('_token', 'language'));

        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    public function information()
    {
        $data = BasicSetting::where('user_id', Auth::guard('web')->user()->id)
            ->first();
        $themes = DB::table('themes')->where('is_active', 1)->get();

        return view('user.settings.information', compact('data', 'themes'));
    }
    public function updateInfo(Request $request)
    {


        $request->validate(
            [
                'website_title' => 'required',
                'preloader_status' => 'required',
                'preloader' => 'nullable|mimes:jpg,png,jpeg,gif',
                'logo' => 'nullable|mimes:jpg,png,jpeg,gif',
                'favicon' => 'nullable|mimes:jpg,png,jpeg,gif',
                'theme_version' => 'required',
                'base_currency_symbol' => 'required',
                'base_currency_symbol_position' => 'required',
                'base_currency_text' => 'required',
                'base_currency_text_position' => 'required',
                'base_currency_rate' => 'required|numeric',
                'primary_color' => 'required',
                'secondary_color' => 'sometimes',
            ]
        );
        $tenantId = Auth::guard('web')->user()->id;
        $basicSettings = BasicSetting::where('user_id', $tenantId)->first();

        $basicSettings->update(
            $request->except(['_token', 'user_id', 'userLanguage', 'logo', 'favicon', 'preloader', 'adminLanguage'] + [
                'user_id' =>  $tenantId
            ])
        );

        $updatedData = [];
        if ($request->hasFile('preloader')) {
            $filename = UploadFile::update(Constant::WEBSITE_PRELOADER, $request->file('preloader'), $basicSettings->preloader);

            if (!empty($basicSettings->preloader)) {
                @unlink(public_path($basicSettings->preloader));
            }
            $updatedData['preloader'] = $filename;
            $updatedData['preloader_status'] = $request->preloader_status;
        }


        if ($request->hasFile('favicon')) {
            $filename = UploadFile::update(Constant::WEBSITE_FAVICON, $request->file('favicon'), $basicSettings->favicon);
            if (!empty($basicSettings->favicon)) {
                @unlink(public_path($basicSettings->favicon));
            }
            $updatedData['favicon'] = $filename;
        }


        if ($request->hasFile('logo')) {
            $filename = UploadFile::update(Constant::WEBSITE_LOGO, $request->file('logo'), $basicSettings->logo);
            if (!empty($basicSettings->logo)) {
                @unlink(public_path($basicSettings->logo));
            }
            $updatedData['logo'] = $filename;
        }

        if (!empty($updatedData)) {
            $basicSettings->update($updatedData);
        }


        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    public function contactForm()
    {
        $data = BasicSetting::where('user_id', Auth::guard('web')->user()->id)
            ->select('email_address', 'contact_number', 'address', 'latitude', 'longitude')
            ->first();
        return view('user.settings.contact-form', ['data' => $data]);
    }

    public function updateContactInfo(Request $request)
    {
        $request->validate(
            [
                'email_address' => 'required',
                'contact_number' => 'required',
                'address' => 'required',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]
        );

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update(
            $request->except(['_token', 'user_id', 'userLanguage'] + [
                'user_id' => Auth::guard('web')->user()->id
            ])
        );
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    public function plugins()
    {
        $data = BasicSetting::query()->where('user_id', Auth::guard('web')->user()->id)
            ->select(
                'disqus_status',
                'disqus_short_name',
                'whatsapp_status',
                'whatsapp_number',
                'whatsapp_header_title',
                'whatsapp_popup_status',
                'whatsapp_popup_message',
                'google_recaptcha_status',
                'google_recaptcha_site_key',
                'google_recaptcha_secret_key',
                'google_login_status',
                'google_client_id',
                'google_client_secret',
                'google_map_api_status',
                'google_map_api_key',
                'google_map_radius',
                'google_map_type',
                'ai_api_key',
                'ai_api_model',
                'ai_api_token',
                'ai_api_temperature',
            )
            ->first();

        return view('user.settings.plugins', ['data' => $data]);
    }

    public function googleapi(Request $request)
    {
        $request->validate(
            [
                'google_map_api_status' => 'required',
                'google_map_api_key' => 'required',
            ]
        );

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update([
            'google_map_api_status' => $request->google_map_api_status,
            'google_map_api_key' => $request->google_map_api_key,
            'google_map_radius' => $request->google_map_radius,
            'google_map_type' => $request->google_map_type,
        ]);
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    public function aiupdate(Request $request)
    {
        $request->validate(
            [
                'ai_api_key' => 'required',
                'ai_api_model' => 'required',
                'ai_api_token' => 'required',
                'ai_api_temperature' => 'required',
            ]
        );

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update([
            'ai_api_key' => $request->ai_api_key,
            'ai_api_model' => $request->ai_api_model,
            'ai_api_token' => $request->ai_api_token,
            'ai_api_temperature' => $request->ai_api_temperature,
        ]);
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }


    public function updateRecapcha(Request $request)
    {
        $request->validate(
            [
                'google_recaptcha_status' => 'required',
                'google_recaptcha_site_key' => 'required_if:google_recaptcha_status,==,1',
                'google_recaptcha_secret_key' => 'required_if:google_recaptcha_status,==,1',
            ],
            [
                'google_recaptcha_site_key.required_if' => 'The google recaptcha site key field is required when google recaptcha status is active.',
                'google_recaptcha_secret_key.required_if' => 'The google recaptcha secret key field is required when google recaptcha status is active.',
            ]
        );

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update([
            'google_recaptcha_status' => $request->google_recaptcha_status,
            'google_recaptcha_site_key' => $request->google_recaptcha_site_key,
            'google_recaptcha_secret_key' => $request->google_recaptcha_secret_key,
        ]);
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }
    public function updateGoogle(Request $request)
    {
        $rules = [
            'google_login_status' => 'required',
            'google_client_id' => 'required_if:google_login_status,==,1',
            'google_client_secret' => 'required_if:google_login_status,==,1'
        ];

        $messages = [
            'google_login_status.required' => 'The login status field is required.',
            'google_client_id.required_if' => 'The client id field is required when login status is active.',
            'google_client_secret.required_if' => 'The client secret field is required when login status is active.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update([
            'google_login_status' => $request->google_login_status,
            'google_client_id' => $request->google_client_id,
            'google_client_secret' => $request->google_client_secret
        ]);
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }
    public function updateDisqus(Request $request)
    {
        $request->validate(
            [
                'disqus_status' => 'required',
                'disqus_short_name' => 'required'
            ],
        );

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update([
            'disqus_status' => $request->disqus_status,
            'disqus_short_name' => $request->disqus_short_name,
        ]);
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    public function updateWhatsApp(Request $request)
    {
        $request->validate(
            [
                'whatsapp_status' => 'required',
                'whatsapp_number' => 'required',
                'whatsapp_header_title' => 'required',
                'whatsapp_popup_status' => 'required',
                'whatsapp_popup_message' => 'required'
            ]

        );

        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update([
            'whatsapp_status' => $request->whatsapp_status,
            'whatsapp_number' => $request->whatsapp_number,
            'whatsapp_header_title' => $request->whatsapp_header_title,
            'whatsapp_popup_status' => $request->whatsapp_popup_status,
            'whatsapp_popup_message' => $request->whatsapp_popup_message,

        ]);
        Session::flash('success', 'WhatsApp info updated successfully!');
        return redirect()->back();
    }


    public function maintenance()
    {
        $data = BasicSetting::where('user_id', Auth::guard('web')->user()->id)
            ->select('maintenance_img', 'maintenance_status', 'maintenance_msg', 'bypass_token')
            ->first();
        return view('user.settings.maintenance', ['data' => $data]);
    }

    public function updateMaintenance(Request $request)
    {
        $data = BasicSetting::query()
            ->where('user_id', Auth::guard('web')->user()->id)
            ->select('maintenance_img')
            ->first();

        $rules = $messages = [];

        if (!$request->filled('maintenance_img') && is_null($data->maintenance_img)) {
            $rules['maintenance_img'] = 'required';
            // $messages['maintenance_img.required'] = 'The maintenance image field is required.';
        }
        if ($request->hasFile('maintenance_img')) {
            $rules['maintenance_img'] = new ImageMimeTypeRule();
        }

        $rules['maintenance_status'] = 'required';
        $rules['maintenance_msg'] = 'required';

        $messages['maintenance_msg.required'] = 'The maintenance message field is required.';
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json(['errors' => $validator->errors(), 'id' => 'footer_logo']);
        }

        if ($request->hasFile('maintenance_img')) {
            $imageName = UploadFile::update(Constant::WEBSITE_MAINTENANCE_IMAGE, $request->file('maintenance_img'), $data->maintenance_img);
        }
        BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update(
            $request->except(['_token', 'maintenance_img', 'maintenance_msg']) + [
                'maintenance_img' => $request->hasFile('maintenance_img') ? $imageName : $data->maintenance_img,
                'maintenance_msg' => Purifier::clean($request->maintenance_msg),
            ]
        );

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }
    public function advertiseSettings()
    {
        $data = BasicSetting::query()
            ->where('user_id', Auth::guard('web')->user()->id)
            ->select('google_adsense_publisher_id')
            ->first();
        return view('user.advertisement.settings', ['data' => $data]);
    }

    public function updateAdvertiseSettings(Request $request)
    {
        $rule = [
            'google_adsense_publisher_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        BasicSetting::query()->updateOrInsert(
            ['user_id' => Auth::guard('web')->user()->id],
            ['google_adsense_publisher_id' => $request->google_adsense_publisher_id]
        );
        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    public function getMailInformation()
    {
        $data['info'] = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('id', 'email', 'from_name')->first();
        return view('user.settings.email.mail-information', $data);
    }

    public function storeMailInformation(Request $request)
    {
        $request->validate(
            [
                'email' => 'required',
                'from_name' => 'required'
            ]
        );
        $info = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->update([
            'email' => $request->email,
            'from_name' => $request->from_name
        ]);

        Session::flash('success', 'Mail information saved successfully!');
        return back();
    }



    public function getSmtpInformation()
    {
        $data['abe'] = SmtpInformation::where([['user_id', Auth::guard('web')->user()->id]])->first();
        return view('user.settings.email.smtpInfo', $data);
    }
}
