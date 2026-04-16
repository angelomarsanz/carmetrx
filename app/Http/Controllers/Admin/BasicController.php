<?php

namespace App\Http\Controllers\Admin;

use App\Models\Seo;
use App\Models\Language;
use App\Models\Timezone;
use App\Constants\Constant;
use App\Models\BasicSetting;
use Illuminate\Http\Request;
use App\Models\BasicExtended;
use App\Traits\AdminLanguage;
use App\Http\Helpers\UploadFile;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BasicController extends Controller
{
    use AdminLanguage;

    public function basicinfo()
    {
        $data['abs'] = BasicSetting::firstOrFail();
        $data['abe'] = BasicExtended::firstOrFail();
        $data['timezones'] = Timezone::all();
        return view('admin.basic.basicinfo', $data);
    }

    public function updatebasicinfo(Request $request)
    {
        $request->validate([
            'website_title' => 'required',
            'timezone' => 'required',
            'preloader_status' => 'required',
            'preloader' => 'sometimes|nullable|mimes:jpg,png,jpeg,gif',
            'logo' => 'sometimes|nullable|mimes:jpg,png,jpeg,gif',
            'favicon' => 'sometimes|nullable|mimes:jpg,png,jpeg,gif',
            'base_color' => 'required',
            'base_currency_symbol' => 'required',
            'base_currency_symbol_position' => 'required',
            'base_currency_text' => 'required',
            'base_currency_text_position' => 'required',
            'base_currency_rate' => 'required|numeric',
        ]);

        $basicFirst = BasicSetting::select('logo', 'favicon', 'preloader')->first();

        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $faviconFilename = uniqid() . '.' . $favicon->getClientOriginalExtension();
            $favicon->move(public_path('assets/front/img/'), $faviconFilename);
            @unlink(public_path('assets/front/img/' . $basicFirst->favicon));
        }

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoFilename = uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('assets/front/img/'), $logoFilename);
            @unlink(public_path('assets/front/img/' . $basicFirst->logo));
        }

        if ($request->hasFile('preloader')) {
            $proloader = $request->file('preloader');
            $proloaderFilename = uniqid() . '.' . $proloader->getClientOriginalExtension();
            $proloader->move(public_path('assets/front/img/'), $proloaderFilename);
            @unlink(public_path('assets/front/img/' . $basicFirst->preloader));
        }

        $bss = BasicSetting::all();
        foreach ($bss as $key => $bs) {
            $bs->website_title = $request->website_title;
            $bs->base_color = $request->base_color;
            $bs->preloader_status = $request->preloader_status;
            if ($request->hasFile('favicon')) {
                $bs->favicon = $faviconFilename;
            }
            if ($request->hasFile('logo')) {
                $bs->logo = $logoFilename;
            }
            if ($request->hasFile('preloader')) {
                $bs->preloader = $proloaderFilename;
            }
            $bs->save();
        }

        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            $be->base_currency_symbol = $request->base_currency_symbol;
            $be->base_currency_symbol_position = $request->base_currency_symbol_position;
            $be->base_currency_text = $request->base_currency_text;
            $be->base_currency_text_position = $request->base_currency_text_position;
            $be->base_currency_rate = $request->base_currency_rate;
            $be->timezone = $request->timezone;
            $be->save();
        }
        // set timezone in .env
        if ($request->has('timezone') && $request->filled('timezone')) {
            $arr = ['TIMEZONE' => $request->timezone];
            setEnvironmentValue($arr);
            // Artisan::call('config:clear');
        }
        Session::flash('success', __('Updated successfully!'));
        return back();
    }


    public function updateslider(Request $request, $lang)
    {
        $be = BasicExtended::where('language_id', $lang)->firstOrFail();


        if ($request->hasFile('slider_shape_img')) {
            @unlink(public_path('assets/front/img/' . $be->slider_shape_img));
            $filename = uniqid() . '.' . $request->slider_shape_img->getClientOriginalExtension();
            $request->slider_shape_img->move('assets/front/img/', $filename);
            $be->slider_shape_img = $filename;
        }

        if ($request->hasFile('slider_bottom_img')) {
            @unlink(public_path('assets/front/img/' . $be->slider_bottom_img));
            $filename = uniqid() . '.' . $request->slider_bottom_img->getClientOriginalExtension();
            $request->slider_bottom_img->move(public_path('assets/front/img/'), $filename);
            $be->slider_bottom_img = $filename;
        }

        $be->save();
        Session::flash('success', __('Updated successfully!'));
        return back();
    }

    public function breadcrumb(Request $request)
    {
        return view('admin.basic.breadcrumb');
    }

    public function updatebreadcrumb(Request $request)
    {
        $img = $request->file('file');
        $allowedExts = array('jpg', 'png', 'jpeg');

        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    if (!empty($img)) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    }
                },
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json(['errors' => $validator->errors(), 'id' => 'breadcrumb']);
        }


        if ($request->hasFile('file')) {

            $filename = uniqid() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('assets/front/img/'), $filename);

            $bss = BasicSetting::all();
            foreach ($bss as $key => $bs) {
                @unlink(public_path('assets/front/img/' . $bs->breadcrumb));
                $bs->breadcrumb = $filename;
                $bs->save();
            }
        }
        Session::flash('success', __('Update successfully!'));
        return back();
    }


    public function script()
    {
        $data = BasicSetting::first();
        return view('admin.basic.scripts', ['data' => $data]);
    }

    public function updatescript(Request $request)
    {

        $bss = BasicSetting::all();

        foreach ($bss as $bs) {
            $bs->tawkto_chat_link = $request->tawkto_chat_link;
            $bs->is_tawkto = $request->is_tawkto;

            $bs->is_disqus = $request->is_disqus;
            $bs->is_user_disqus = $request->is_user_disqus;
            $bs->disqus_shortname = $request->disqus_shortname;

            $bs->is_recaptcha = $request->is_recaptcha;
            $bs->google_recaptcha_site_key = $request->google_recaptcha_site_key;
            $bs->google_recaptcha_secret_key = $request->google_recaptcha_secret_key;

            $bs->is_whatsapp = $request->is_whatsapp;
            $bs->whatsapp_number = $request->whatsapp_number;
            $bs->whatsapp_header_title = $request->whatsapp_header_title;
            $bs->whatsapp_popup_message = Purifier::clean($request->whatsapp_popup_message);
            $bs->whatsapp_popup = $request->whatsapp_popup;
            
            $bs->ai_api_key = $request->ai_api_key;
            $bs->ai_api_model = $request->ai_api_model;
            $bs->ai_api_token = $request->ai_api_token;
            $bs->ai_api_temperature > $request->ai_api_temperature;

            $bs->save();
        }

        Session::flash('success', __('Updated successfully!'));
        return back();
    }


    public function maintainance()
    {
        $data = BasicSetting::select('maintainance_mode', 'maintenance_img', 'maintenance_status', 'maintainance_text', 'secret_path')
            ->first();

        return view('admin.basic.maintainance', ['data' => $data]);
    }

    public function updatemaintainance(Request $request)
    {
        $img = $request->file('file');
        $allowedExts = array('jpg', 'png', 'jpeg');

        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    if (!empty($img)) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    }
                },
            ],
            'maintenance_status' => 'required',
            'maintainance_text' => 'required'
        ];

        $message = [
            'maintainance_text.required' => __('The maintenance message field is required')
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $bs = BasicSetting::first();

        // first, get the maintenance image from db
        if ($request->hasFile('file')) {
            $filename = uniqid() . '.' . $request->file('file')->getClientOriginalExtension();

            @unlink(public_path("assets/front/img/" . $bs->maintenance_img));
            $request->file('file')->move(public_path('assets/front/img/'), $filename);
        }


        $down = "down";
        if ($request->filled('secret_path')) {
            $down .= " --secret=" . $request->secret_path;
        }

        if ($request->maintenance_status == 1) {
            @unlink('storage/framework/down');
            Artisan::call($down);
        } else {
            Artisan::call('up');
        }


        $bs->update([
            'maintenance_img' => $request->hasFile('file') ? $filename : $bs->maintenance_img,
            'maintenance_status' => $request->maintenance_status,
            'maintainance_text' => clean($request->maintainance_text),
            'secret_path' => $request->secret_path
        ]);

        $request->session()->flash('success', __('Updated successfully!'));

        return redirect()->back();
    }


    public function aboutSectionInfo()
    {

        $data['abs'] = BasicSetting::first();
        if (!is_null($data['abs']->about_additional_section_status) && $data['abs']->about_additional_section_status != "null") {
            $data['additional_section_statuses'] = json_decode($data['abs']->about_additional_section_status, true);
        } else {
            $data['additional_section_statuses'] = [];
        }
        $language = $this->defaultLang();
        $data['langid'] = $language->id;
        return view('admin.about-page.sections', $data);
    }

    public function aboutSectionInfoUpdate(Request $request)
    {

        $bss = BasicSetting::all();
        $in = $request->all();
        $in['about_additional_section_status'] = json_encode($request->additional_sections, true);
        unset($in['additional_sections']);
        foreach ($bss as $key => $bs) {
            $bs->update($in);
        }
        Session::flash('success', __('Updated Successfully'));
        return back();
    }

    public function sections(Request $request)
    {
        $data['abs'] = BasicSetting::first();

        if (! is_null($data['abs']->additional_section_status) && $data['abs']->additional_section_status != "null") {
            $data['additional_section_statuses'] = json_decode($data['abs']->additional_section_status, true);
        } else {
            $data['additional_section_statuses'] = [];
        }
        // dd($data['additional_section_statuses']);
        $data['languge_id'] = $this->defaultLang()->id;
        return view('admin.home.sections', $data);
    }

    public function updatesections(Request $request)
    {
        $bss = BasicSetting::all();

        $in = $request->all();
        $in['additional_section_status'] = json_encode($request->additional_sections, true);
        unset($in['additional_sections']);
        foreach ($bss as $key => $bs) {
            $bs->update($in);
        }
        Session::flash('success', __('Updated successfully!'));
        return back();
    }


    public function cookiealert(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abe'] = $lang->basic_extended;

        return view('admin.basic.cookie', $data);
    }

    public function updatecookie(Request $request, $langid)
    {
        $request->validate([
            'cookie_alert_status' => 'required',
            'cookie_alert_text' => 'required',
            'cookie_alert_button_text' => 'required|max:25',
        ]);

        $be = BasicExtended::where('language_id', $langid)->firstOrFail();
        $be->cookie_alert_status = $request->cookie_alert_status;
        $be->cookie_alert_text = Purifier::clean($request->cookie_alert_text);
        $be->cookie_alert_button_text = $request->cookie_alert_button_text;
        $be->save();

        Session::flash('success', __('Updated successfully!'));
        return back();
    }

    public function seo(Request $request)
    {
        // first, get the language info from db
        $language = $this->selectLang($request->language);
        $langId = $language->id;

        // then, get the seo info of that language from db
        $seo = Seo::where('language_id', $langId);

        if ($seo->count() == 0) {
            // if seo info of that language does not exist then create a new one
            Seo::create($request->except('language_id') + [
                'language_id' => $langId
            ]);
        }

        $information['language'] = $language;

        // then, get the seo info of that language from db
        $information['data'] = $seo->first();

        // get all the languages from db
        // $information['langs'] = Language::all();

        return view('admin.basic.seo', $information);
    }

    public function updateSEO(Request $request)
    {
        // first, get the language info from db
        $language = Language::where('code', $request->language)->first();
        $langId = $language->id;

        // then, get the seo info of that language from db
        $seo = SEO::where('language_id', $langId)->first();

        // else update the existing seo info of that language
        $seo->update($request->all());

        $request->session()->flash('success', __('Updated successfully!'));

        return redirect()->back();
    }

    public function userThemes()
    {
        $themes = DB::table('themes')->get();
        return view('admin.home.templates.index', compact('themes'));
    }

    public function userThemeStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg,svg',
            'url' => 'required',
            'serial_number' => 'required|min:1|numeric'
        ]);
        if ($request->hasFile('image')) {
            $image = UploadFile::store(Constant::WEBSITE_THEMES, $request->image);
        }
        DB::table('themes')->insert([
            'image' => $image,
            'name' => $request->name,
            'url' => $request->url,
            'serial_number' => $request->serial_number
        ]);
        session()->flash('success', __('Updated successfully!'));
        return 'success';
    }
    public function userThemeUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'image|mimes:jpg,png,jpeg,svg',
            'url' => 'required',
            'serial_number' => 'required|min:1|numeric'
        ]);

        $theme = DB::table('themes')->find($request->theme_id);
        $image = $theme->image;
        if ($request->hasFile('image')) {
            @unlink(public_path(Constant::WEBSITE_THEMES . '/' . $theme->image));
            $image = UploadFile::store(Constant::WEBSITE_THEMES, $request->image);
        }
        DB::table('themes')->where('id', $request->theme_id)->update([
            'name' => $request->name,
            'url' => $request->url,
            'image' => $image,
            'serial_number' => $request->serial_number
        ]);
        session()->flash('success', __('Updated successfully!'));

        return 'success';
    }
    public function themeDelete(Request $request)
    {
        $theme = DB::table('themes')->where('id', $request->theme_id)->first();

        if ($theme) {
            @unlink(public_path(Constant::WEBSITE_THEMES . '/' . $theme->image));

            DB::table('themes')->where('id', $request->theme_id)->delete();

            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('error', __('Theme not found!'));
        }

        return back();
    }


    public function changeThemeStatus(Request $request)
    {
        DB::table('themes')->where('id', $request->theme_id)->update([

            'is_active' => $request->status,
        ]);
        session()->flash('success', __('Updated successfully!'));

        return redirect()->back();
    }
}
