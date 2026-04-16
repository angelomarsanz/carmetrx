<?php

namespace App\Http\Controllers\User\HomePage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\User\BasicSetting;
use App\Models\User\HomePage\AboutSection;
use App\Models\User\HomePage\HomePage;
use App\Models\User\HomePage\NewsletterSection;
use App\Models\User\HomePage\SectionTitle;
use App\Models\User\HomePage\TestimonialSection;
use App\Models\User\HomePage\VideoSection;
use App\Models\User\HomePage\WhyChooseUsSection;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SectionTitleController extends Controller
{
    use TenantFrontendLanguage;

    public function imagesTexts(Request $request)
    {
        $userId =  Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = $this->selectLang($userId, $request->language);
        } else {
            $language = $this->currentLang($userId);
        }

        $information['data'] = $language->sectionTitle()->first();
        $information['homePage'] = HomePage::where('user_id', $userId)->first();
        $information['themeVersion'] = BasicSetting::where('user_id', $userId)->pluck('theme_version')->first();
        $information['testimonialInfo'] =  $language->testimonialSecInfo()->first();
        $information['newsletterInfo'] =  $language->newsletterSection()->first();
        $information['videoInfo'] = $language->videoSection()->first();
        $information['whyChooseUsSection'] = $language->whyChooseUsSection()->first();
        $information['aboutSection'] = $language->aboutSection()->first();
        return view('user.home-page.image&text', $information);
    }

    public function updateImagesTexts(Request $request)
    {

        DB::transaction(function () use ($request) {

            $userId =  Auth::guard('web')->user()->id;
            $language = $this->selectLang($userId, $request->language);
            $homePage = HomePage::firstOrNew(['user_id' => $userId]);

            SectionTitle::query()->updateOrCreate(
                [
                    'language_id' => $language->id,
                    'user_id' => $userId
                ],
                $request->only([
                    'category_section_title',
                    'category_section_subtitle',
                    'property_section_title',
                    'featured_property_section_title',
                    'project_section_title',
                    'project_section_subtitle',
                    'agent_section_title',
                    'agent_section_subtitle',
                    'city_section_title',
                    'city_section_subtitle', 
                ])
            );






            if ($request->has('newsletter_subtitle') || $request->has('newsletter_title') || $request->has('newsletter_button_name')) {

                NewsletterSection::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'language_id' => $language->id
                    ],
                    [
                        'subtitle' => $request->newsletter_subtitle,
                        'title' => $request->newsletter_title,
                        'btn_name' => $request->newsletter_button_name,
                    ]
                );
            }

            if ($request->has('videosection_title') || $request->has('videosection_subtitle') || $request->has('videosection_video_url') || $request->has('videosection_text')) {

                VideoSection::updateOrCreate(
                    [
                        'language_id' => $language->id,
                        'user_id' =>  $userId
                    ],
                    [
                        'title' => $request->videosection_title,
                        'subtitle' => $request->videosection_subtitle,
                        'url' => $request->videosection_video_url,
                        'text' => $request->videosection_text,
                    ]
                );
            }

            // Homepage Images
            $imageFields = [
                'city_bg_img' => Constant::WEBSITE_CITY_SECTION_IMAGE,
                'counter_bg_img' => Constant::WEBSITE_COUNTER_SEC_IMAGE, 
                'newsletter_bg_img' => Constant::WEBSITE_NEWSLETTER_IMAGE,
                'video_bg_img' => Constant::WEBSITE_VIDEO_SECTION_IMAGE,
            ];

            foreach ($imageFields as $field => $path) {
                $this->handleImage($request, $field, $path, $homePage);
            }

            $homePage->save();
        });

        Session::flash('success', __('Updated successfully!'));

        return redirect()->back();
    }

    private function handleImage($request, $field, $path, &$model)
    {
        if ($request->hasFile($field)) {
            $newImage = $request->file($field);
            $oldImage = $model?->$field ?? '';
            $imgName = UploadFile::update($path . '/', $newImage, $oldImage);
            $model->$field = $imgName;
        }
    }
}
