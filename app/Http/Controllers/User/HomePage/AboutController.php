<?php

namespace App\Http\Controllers\User\HomePage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\User\HomePage\AboutSection;
use App\Models\User\HomePage\HomePage;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    use TenantFrontendLanguage;

    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id; 
        $language = $this->selectLang($tenantId, $request->language);

        $information['info'] = HomePage::where('user_id', $tenantId)->first(['about_section_image', 'about_section_image2', 'about_section_video_link']);

        $information['data'] = AboutSection::where([['user_id', $tenantId],  ['language_id', $language->id]])->first();

        return view('user.home-page.about-section', $information);
    }

    public function updateImage(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $info =  HomePage::where('user_id', $tenantId)->first(['id', 'about_section_image', 'about_section_image2']);



        $rules = [];

        if (empty($info->about_section_image)) {
            $rules['about_section_image'] = 'required';
        }
        if ($request->hasFile('about_section_image')) {
            $rules['about_section_image'] = new ImageMimeTypeRule();
        }

        if (empty($info->about_section_image2)) {
            $rules['about_section_image2'] = 'required';
        }
        if ($request->hasFile('about_section_image2')) {
            $rules['about_section_image2'] = new ImageMimeTypeRule();
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if ($request->hasFile('about_section_image')) {
            $newImage = $request->file('about_section_image');
            $oldImage = $info->about_section_image ?? '';

            $imgName = UploadFile::update(Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/', $newImage, $oldImage);
        }

        if ($request->hasFile('about_section_image2')) {
            $newImage2 = $request->file('about_section_image2');
            $oldImage2 = $info->about_section_image2 ?? '';

            $imgName2 = UploadFile::update(Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/', $newImage2, $oldImage2);
        }

        $link = $request->about_section_video_link;

        if (strpos($link, '&') != 0) {
            $endPosition = strpos($link, '&');
            $link = substr($link, 0, $endPosition);
        }

        HomePage::updateOrCreate(
            [
                'user_id' => $tenantId,
            ],
            [
                'about_section_image' => isset($imgName) ? $imgName : $info->about_section_image,
                'about_section_image2' => isset($imgName2) ? $imgName2 : $info->about_section_image2,
                'about_section_video_link' => isset($link) ? $link : null
            ]
        );

        session()->flash('success', __('Updated successfully!'));

        return redirect()->back();
    }


    public function updateInfo(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);
        AboutSection::query()->updateOrCreate(
            [
                'language_id' => $language->id,
                'user_id' =>  $userId
            ],
            [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->text,
                'client_text' => $request->client_text,
                'btn_name' => $request->button_name,
                'btn_url' => $request->button_url,
                'years_of_expricence' => $request->years_of_expricence
            ]
        );

        session()->flash('success', __('Updated successfully!'));

        return redirect()->back();
    }
}
