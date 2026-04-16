<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasicExtended;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HerosectionController extends Controller
{
    use AdminLanguage;
    public function imgtext(Request $request)
    {
        $lang = $this->selectLang($request->language);
        $data['lang_id'] = $lang->id;
        $data['abe'] = $lang->basic_extended;

        return view('admin.home.hero.img-text', $data);
    }

    public function update(Request $request, $langid)
    {
        $sideImg = $request->file('image');
        $background_image = $request->file('background_image');
        $allowedExts = array('jpg', 'png', 'jpeg');

        $rules = [
            'image' => [
                function ($attribute, $value, $fail) use ($request, $sideImg, $allowedExts) {
                    if ($request->hasFile('image')) {
                        $ext = $sideImg->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail(__("Only png, jpg, jpeg image is allowed"));
                        }
                    }
                },
            ],
            'background_image' => [
                function ($attribute, $value, $fail) use ($request, $background_image, $allowedExts) {
                    if ($request->hasFile('image')) {
                        $ext = $background_image->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail(__("Only png, jpg, jpeg image is allowed"));
                        }
                    }
                },
            ],
            'hero_section_title' => 'nullable|max:255',
            'hero_section_text' => 'nullable',
            'hero_section_button_text' => 'nullable|max:30',
            'hero_section_button_url' => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }


        $be = BasicExtended::where('language_id', $langid)->firstOrFail();
        $be->hero_section_title = $request->hero_section_title;
        $be->hero_section_text = $request->hero_section_text;
        $be->hero_section_button_text = $request->hero_section_button_text;
        $be->hero_section_button_url = $request->hero_section_button_url;
        $be->hero_section_snd_btn_text = $request->hero_section_snd_btn_text;
        $be->hero_section_snd_btn_url = $request->hero_section_snd_btn_url;

        if ($request->hasFile('image')) {
            @unlink(public_path('assets/front/img/' . $be->hero_img));
            $filename = uniqid() . '.' . $sideImg->getClientOriginalExtension();
            $sideImg->move(public_path('assets/front/img/'), $filename);
            $be->hero_img = $filename;
        }
        if ($request->hasFile('background_image')) {
            @unlink(public_path('assets/front/img/' . $be->hero_bg_img));
            $filename = uniqid() . '.' . $background_image->getClientOriginalExtension();
            $background_image->move(public_path('assets/front/img/'), $filename);
            $be->hero_bg_img = $filename;
        }

        $be->save();

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function video(Request $request)
    {
        $data['abe'] = BasicExtended::first();

        return view('admin.home.hero.video', $data);
    }

    public function videoupdate(Request $request)
    {
        $rules = [
            'video_link' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $bes = BasicExtended::all();

        $videoLink = $request->video_link;
        if (strpos($videoLink, "&") != false) {
            $videoLink = substr($videoLink, 0, strpos($videoLink, "&"));
        }

        foreach ($bes as $key => $be) {
            # code...
            $be->hero_section_video_link = $videoLink;
            $be->save();
        }

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }
}
