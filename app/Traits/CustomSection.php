<?php

namespace App\Traits;

trait CustomSection
{
    public static function AdminFrontHomePage()
    {
        return [
            'hero_section',
            'partners_section',
            'work_process_section',
            'template_section',
            'intro_section',
            'pricing_section',
            'featured_users_section',
            'testimonial_section',
            'blog_section',
        ];
    }
    public static function AdminFrontAboutPage()
    {
        return [
            'intro_section',
            'work_process_section',
            'testimonial_section',
        ];
    }


    public static function TenantFrontThemeOne()
    {
        return [
            'hero_section',
            'counter_section',
            'featured_properties_section',
            'about_section',
            'property_section',
            'why_choose_us_section',
            'agent_section',
            'cities_section',
            'testimonial_section',
            'newsletter_section',
        ];
    }
    public static function TenantFrontThemeTwo()
    {
        return [
            'hero_section',
            'category_section',
            'featured_properties_section',
            'video_section',
            'property_section',
            'work_steps_section',
            'testimonial_section',
            'partner_section',
        ];
    }
    public static function TenantFrontThemeThree()
    {
        return [
            'hero_section',
            'partner_section',
            'category_section',
            'property_section',
            'about_section',
            'work_steps_section',
            'counter_section',
            'project_section',
            'testimonial_section',
        ];
    }
    public static function AboutUsPage()
    {
        return [
            'about_info_section',
            'why_choose_us_section',
            'work_steps_section',
            'testimonial_section',
        ];
    }
}
