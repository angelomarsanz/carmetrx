<?php

namespace App\Models\User\HomePage;

use App\Constants\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    use HasFactory;

    public $table = 'user_home_pages';
    protected $guarded = [];

    public function deleteHomePage()
    {

        $homePage = $this;
        @unlink(public_path(Constant::WEBSITE_SLIDER_IMAGE . '/' . $homePage->hero_static_img));
        @unlink(public_path(Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/' . $homePage->about_section_image));
        @unlink(public_path(Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/' . $homePage->about_section_image2));
        @unlink(public_path(Constant::WEBSITE_TESTIMONIAL_SECTION_IMAGE . '/' . $homePage->testimonial_bg_img));
        @unlink(public_path(Constant::WEBSITE_NEWSLETTER_IMAGE . '/' . $homePage->newsletter_bg_img));
        @unlink(public_path(Constant::WEBSITE_WHY_CHOOSE_US_SECTION_IMAGE . '/' . $homePage->why_choose_us_section_img1));
        @unlink(public_path(Constant::WEBSITE_WHY_CHOOSE_US_SECTION_IMAGE . '/' . $homePage->why_choose_us_section_img2));
        @unlink(public_path(Constant::WEBSITE_WORK_PROCESS_IMAGE . '/' . $homePage->work_process_bg_img));
        @unlink(public_path(Constant::WEBSITE_VIDEO_SECTION_IMAGE . '/' . $homePage->video_bg_img));
        @unlink(public_path(Constant::WEBSITE_CITY_SECTION_IMAGE . '/' . $homePage->city_section_subtitle));
        @unlink(public_path(Constant::WEBSITE_COUNTER_SEC_IMAGE . '/' . $homePage->counter_bg_img));

        return;
    }
}
