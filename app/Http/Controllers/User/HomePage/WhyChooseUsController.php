<?php

namespace App\Http\Controllers\User\HomePage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\User\HomePage\HomePage;
use App\Models\User\HomePage\WhyChooseUsSection;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WhyChooseUsController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $information['info'] = HomePage::where('user_id', $userId)
            ->select('why_choose_us_section_img1', 'why_choose_us_section_img2', 'why_choose_us_section_video_link')
            ->first();

        $language =  $this->selectLang($userId, $request->language);
        $information['language'] = $language;

        $information['data'] = $language->whyChooseUsSection()->first();

        $information['langs'] = $this->allLangs($userId);

        return view('user.home-page.whyChooseUsSection', $information);
    }

    public function updateImage(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $info = HomePage::where('user_id', $userId)
            ->select('id', 'why_choose_us_section_img1', 'why_choose_us_section_img2', 'why_choose_us_section_video_link')
            ->first();

        $rules = [];

        if (empty($info->why_choose_us_section_img1)) {
            $rules['why_choose_us_section_img1'] = 'required';
        }
        if ($request->hasFile('why_choose_us_section_img1')) {
            $rules['why_choose_us_section_img1'] = new ImageMimeTypeRule();
        }

        if (empty($info->why_choose_us_section_img2)) {
            $rules['why_choose_us_section_img2'] = 'required';
        }
        if ($request->hasFile('why_choose_us_section_img2')) {
            $rules['why_choose_us_section_img2'] = new ImageMimeTypeRule();
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if ($request->hasFile('why_choose_us_section_img1')) {
            $newImage1 = $request->file('why_choose_us_section_img1');
            $oldImage = $info->why_choose_us_section_img1;

            $imgName1 = UploadFile::update(Constant::WEBSITE_WHY_CHOOSE_US_SECTION_IMAGE, $newImage1, $oldImage);
        }

        if ($request->hasFile('why_choose_us_section_img2')) {
            $newImage2 = $request->file('why_choose_us_section_img2');
            $oldImage2 = $info->why_choose_us_section_img2;

            $imgName2 = UploadFile::update(Constant::WEBSITE_WHY_CHOOSE_US_SECTION_IMAGE, $newImage2, $oldImage2);
        }


        $link = $request->why_choose_us_section_video_link;

        if (strpos($link, '&') != 0) {
            $endPosition = strpos($link, '&');
            $link = substr($link, 0, $endPosition);
        }


        $info->update([
            'why_choose_us_section_img1' => isset($imgName1) ? $imgName1 : $info->why_choose_us_section_img1,
            'why_choose_us_section_img2' => isset($imgName2) ? $imgName2 : $info->why_choose_us_section_img2,
            'why_choose_us_section_video_link' => isset($link) ? $link : null
        ]);

        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }


    public function updateInfo(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);

        WhyChooseUsSection::query()->updateOrCreate(
            [
                'language_id' => $language->id,
                'user_id' => $userId
            ],
            [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->text,

            ]
        );

        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }
}
