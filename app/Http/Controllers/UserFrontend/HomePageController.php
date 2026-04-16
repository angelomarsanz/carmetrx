<?php

namespace App\Http\Controllers\UserFrontend;

use App\Traits\CustomSection;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Models\User\Property\City;
use App\Models\User\Property\State;
use App\Http\Controllers\Controller;
use App\Models\User\Project\Project;
use App\Models\User\HomePage\Partner;
use App\Models\User\HomePage\Section;
use App\Models\User\Property\Country;
use App\Models\User\AdditionalSection;
use App\Models\User\HomePage\HomePage;
use App\Models\User\Property\Category;
use App\Models\User\Property\Property;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Models\User\HomePage\WhyChooseUsSection;
use App\Models\User\Project\Category as ProjectCategory;
use App\Http\Controllers\UserFrontend\MiscellaneousController;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;

class HomePageController extends Controller
{
    use TenantFrontendLanguage, PageHeadings, CustomSection;
    public function index()
    {
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);
        $adminLang = getPropertyspectLang($language);
        $queryResult['adminLang'] = $adminLang;
        $homePage = HomePage::query()->where('user_id', $tenantId);
        $basicSetings = BasicSetting::where('user_id', $tenantId);
        $themeVersion = $basicSetings->first('theme_version')->theme_version;
        $sectionTitle = $language->sectionTitle();

        $queryResult['secInfo'] = Section::where('user_id', $tenantId)->first();

        $queryResult['seoInfo'] = $language->seoInfo;

        if ($themeVersion != 2) {
            $queryResult['heroStatic'] = $language->heroStatic;
            $queryResult['heroImg'] = $homePage->pluck('hero_static_img')->first() ?? '';
        } else {
            $queryResult['sliderInfos'] = $language->heroSlider;
        }

        if ($themeVersion == 1 && $queryResult['secInfo']->counter_section_status == 1) {
            $queryResult['counters'] =  $language->counters;
        }

        if ($themeVersion != 3) {
            $queryResult['featuredSecInfo'] = $language->featuredSecInfo;
        }

        if ($queryResult['secInfo']->about_section_status == 1) {
            $queryResult['aboutImg'] = $homePage->select('about_section_image', 'about_section_image2', 'about_section_video_link')->first();

            $queryResult['aboutInfo'] = $language->aboutSection;
        }

        if ($queryResult['secInfo']->property_section_status == 1) {
            $queryResult['propertySecInfo'] = $language->propertySecInfo;
        }



        if ($themeVersion == 1 && $queryResult['secInfo']->why_choose_us_section_status == 1) {
            $queryResult['whyChooseUsImg'] = $homePage->select('why_choose_us_section_img1', 'why_choose_us_section_img2', 'why_choose_us_section_video_link')->first();
            $queryResult['whyChooseUsInfo'] = $language->whyChooseUsSection;
        }


        if ($themeVersion == 1 && $queryResult['secInfo']->agent_section_status == 1) {

            $queryResult['agentInfo'] =  $language->agentSecInfo;

            $queryResult['agents'] = Agent::where([['status', 1], ['user_id', $tenantId]])->with(['agentInfo' => function ($q) use ($language) {
                $q->where('language_id', $language->id);
            },])->inRandomOrder()->take(3)->get();
        }

        if ($themeVersion == 1 && $queryResult['secInfo']->cities_section_status == 1) {
            $queryResult['cityBgImg'] = $homePage->select('city_bg_img')->first()?->city_bg_img;
            $queryResult['citySecInfo'] = $sectionTitle->select('city_section_title', 'city_section_subtitle')->first();
            $cities =  City::where([['status', 1], ['featured', 1], ['user_id', $tenantId]])->limit(6)->orderBy('serial_number', 'asc')->get();
            $cities->map(function ($city) use ($language) {
                $city['propertyCount'] = $city->properties()->count();
                $city['name'] = $city->getContent($language->id)?->name;
                $city['slug'] = $city->getContent($language->id)?->slug;
            });

            $queryResult['cities'] =  $cities;
        }

        if ($queryResult['secInfo']->features_section_status == 1) {
            $queryResult['allFeature'] = $language->features()->orderByDesc('id')->get();
        }


        $queryResult['currencyInfo'] = $basicSetings->select('base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate')
            ->first();

        if ($queryResult['secInfo']->testimonial_section_status == 1) {
            $queryResult['testimonialSecInfo'] = $language->testimonialSecInfo;
            $queryResult['testimonials'] = $language->testimonials()->orderByDesc('id')->get();
            $queryResult['testimonialSecImage'] = $homePage->select('testimonial_bg_img')->first()?->testimonial_bg_img;
        }


        if ($queryResult['secInfo']->partners_section_status == 1) {
            $queryResult['partners'] = Partner::where('user_id', $tenantId)->orderByDesc('serial_number', 'asc')->get();
        }


        if ($themeVersion == 1 && $queryResult['secInfo']->newsletter_section_status == 1) {
            $queryResult['newsletterBgImg'] = $homePage->select('newsletter_bg_img')->first()?->newsletter_bg_img;

            $queryResult['newsletterSecInfo'] = $language->newsletterSection;
        }

        if (($themeVersion == 2 || $themeVersion == 3) && $queryResult['secInfo']->partner_section_status == 1) {
            $queryResult['partners'] = Partner::where('user_id', $tenantId)->orderByDesc('serial_number', 'asc')->get();
        }

        $adminLang = getPropertyspectLang($language);
        if ($themeVersion == 2 || $themeVersion == 3) {
            $queryResult['catgorySecInfo'] = $language->categorySecInfo;

            if (!is_null($adminLang)) {
                $queryResult['property_categories'] = Category::where([['status', 1], ['featured', 1], ['user_id', 0]])
                    ->with(['categoryContent' => function ($q) use ($adminLang) {
                        $q->where('language_id', $adminLang->id);
                    }])->orderBy('serial_number', 'asc')->get();
            } else {
                $queryResult['property_categories'] = [];
            }
        }

        if ($queryResult['secInfo']->work_steps_section_status == 1) {
            $queryResult['workStepsSecImg'] = $homePage->select('work_process_bg_img')->first()?->work_process_bg_img;
            $queryResult['workStepsSecInfo'] = $language->workStepsSecInfo;
            $queryResult['steps'] = $language->workSteps()->orderBy('serial_number', 'asc')->get();
        }

        if ($queryResult['secInfo']->counter_section_status == 1) {
            $queryResult['counterSectionImage'] = $homePage->select('counter_bg_img')->first()?->counter_bg_img;
            $queryResult['counters'] = $language->counterInfos()->get();
        }

        if ($themeVersion == 3 && $queryResult['secInfo']->project_section_status == 1) {

            $queryResult['projectCategories'] = ProjectCategory::where('featured', 1)->getCategories($tenantId, $language->id);

            $projectCate  = ProjectCategory::where('user_id', $tenantId)->where('featured', 1)->pluck('id')->toArray();

            $queryResult['allFeaturedProjects'] = Project::where('user_projects.user_id', $tenantId)
                ->leftJoin('user_project_contents', 'user_project_contents.project_id', 'user_projects.id')
                ->whereIn('user_projects.category_id', $projectCate)
                ->where('user_projects.featured', 1)
                ->with(['user', 'agent'])
                ->where('user_project_contents.language_id', $language->id)
                ->select('user_projects.*', 'user_project_contents.slug', 'user_project_contents.title', 'user_project_contents.address')->latest()->get();

            $queryResult['featuredProjectCate'] = ProjectCategory::where('user_project_categories.featured', 1)
                ->where('user_project_categories.user_id', $tenantId)
                ->with([
                    'projects' => function ($query) use ($language) {
                        $query->leftJoin('user_project_contents', 'user_project_contents.project_id', 'user_projects.id')
                            ->where('user_projects.featured', 1)
                            ->where('user_project_contents.language_id', $language->id)
                            ->with(['user', 'agent'])
                            ->select('user_projects.*', 'user_project_contents.slug', 'user_project_contents.title', 'user_project_contents.address')
                            ->latest();
                    }
                ])->get();
            $queryResult['projectInfo'] = $language->projectSecInfo;
        }


        if ($themeVersion == 2 && $queryResult['secInfo']->video_section_status == 1) {
            $queryResult['vodeoSecImg'] = $homePage->select('video_bg_img')->first()?->video_bg_img;
            $queryResult['videoSecInfo'] = $language->videoSection;
        }



        if (!is_null($adminLang)) {
            $all_proeprty_categories = Category::where([['status', 1], ['user_id', 0]])
                ->orderBy('serial_number', 'asc')
                ->get();
            $queryResult['all_proeprty_categories'] = $all_proeprty_categories;
        } else {
            $queryResult['all_proeprty_categories'] = [];
        }

        $queryResult['all_cities'] = City::where([['status', 1], ['user_id', 0]])->orderBy('serial_number', 'asc')->get();
        $queryResult['all_states'] = State::where('user_id', 0)->get();
        $queryResult['all_countries'] = Country::where('user_id', 0)->get();



        $properties = Property::where('user_properties.status', 1)
            ->where('user_properties.user_id', $tenantId)
            ->where('user_property_contents.language_id', $language->id)
            ->join('user_property_contents', 'user_property_contents.property_id', 'user_properties.id')
            ->join('user_property_categories', 'user_property_categories.id', 'user_properties.category_id')

            ->select('user_properties.*', 'user_property_contents.language_id', 'user_property_contents.slug', 'user_property_contents.title', 'user_property_contents.address', 'user_property_contents.language_id')->latest()->take(8)->get();

        $queryResult['properties'] = $properties;

        $queryResult['featured_properties'] = Property::where('user_properties.status', 1)
            ->where('user_properties.user_id', $tenantId)
            ->where('user_properties.featured', 1)
            ->leftJoin('user_property_contents', 'user_property_contents.property_id', 'user_properties.id')
            ->leftJoin('user_property_categories', 'user_property_categories.id', 'user_properties.category_id')

            ->where('user_property_contents.language_id', $language->id)
            ->select(
                'user_properties.*',
                'user_property_contents.slug',
                'user_property_contents.title',
                'user_property_contents.address',
                'user_property_contents.language_id'
            )
            ->inRandomOrder()
            ->take(10)
            ->get();




        $queryResult['themeVersion'] = $themeVersion;
        $min =  Property::where([['status', 1]])->where('user_id', $tenantId)->min('price');
        $max = Property::where([['status', 1]])->where('user_id', $tenantId)->max('price');
        $queryResult['min'] = intval($min);
        $queryResult['max'] = intval($max);

        $pageType = 'home';
        if ($themeVersion == 1) {
            $sections = CustomSection::TenantFrontThemeOne();
        }
        if ($themeVersion == 2) {
            $sections = CustomSection::TenantFrontThemeTwo();
        }
        if ($themeVersion == 3) {
            $sections = CustomSection::TenantFrontThemeThree();
        }

        foreach ($sections as $section) {
            $queryResult["after_" . str_replace('_section', '', $section)] = AdditionalSection::where('possition', $section)
                ->where('page_type', $pageType)
                ->orderBy('serial_number', 'asc')
                ->get();
        }

        $sectionInfo = BasicSetting::where('user_id', $tenantId)->select('additional_section_status')->first();
        if (!empty($sectionInfo->additional_section_status)) {
            $info = json_decode($sectionInfo->additional_section_status, true);
            $queryResult['homecusSec'] = $info;
        }

        if ($themeVersion == 1) {
            return view('tenant_frontend.home.index-v1', $queryResult);
        } else if ($themeVersion == 2) {
            return view('tenant_frontend.home.index-v2', $queryResult);
        } elseif ($themeVersion == 3) {
            return view('tenant_frontend.home.index-v3', $queryResult);
        }
    }

    public function aboutus($username)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();
        $language = $this->currentLang($tenantId);
        $queryResult['pageHeading'] = $this->pageHeading($tenantId);
        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_about_page', 'meta_description_about_page')->first();
        $homePage = HomePage::query()->where('user_id', $tenantId);

        $queryResult['bgImg'] = $misc->getBreadcrumb($tenantId);
        $queryResult['secInfo'] = BasicSetting::where('user_id', $tenantId)->select('about_info_section', 'why_choose_us_section', 'work_steps_section', 'testimonial_section')->first();


        if ($queryResult['secInfo']->about_info_section == 1) {
            $queryResult['aboutImg'] = $homePage->select('about_section_image', 'about_section_image2', 'about_section_video_link')->first();
            $queryResult['aboutInfo'] = $language->aboutSection()->first();
        }

        if ($queryResult['secInfo']->why_choose_us_section == 1) {
            $queryResult['whyChooseUsImg'] = $homePage->select('why_choose_us_section_img1', 'why_choose_us_section_img2', 'why_choose_us_section_video_link')->first();
            $queryResult['whyChooseUsInfo'] =  WhyChooseUsSection::where([['user_id', $tenantId], ['language_id', $language->id]])->first();
        }

        if ($queryResult['secInfo']->work_steps_section == 1) {
            $queryResult['workStepsSecImg'] = $homePage->select('work_process_bg_img')->first()?->work_process_bg_img ?? null;
            $queryResult['workStepsSecInfo'] = $language->workStepsSecInfo()->first();
            $queryResult['steps'] = $language->workSteps()->orderBy('serial_number', 'asc')->get();
        }

        if ($queryResult['secInfo']->testimonial_section == 1) {
            $queryResult['testimonialSecInfo'] = $language->testimonialSecInfo()->first();
            $queryResult['testimonials'] = $language->testimonials()->orderByDesc('id')->get();
            $queryResult['testimonialSecImage'] = $homePage->select('testimonial_bg_img')->first()?->testimonial_bg_img ?? null;
        }

        $pageType = 'about';
        $sections = CustomSection::AboutUsPage();
        foreach ($sections as $section) {
            $queryResult["after_" . str_replace('_section', '', $section)] = AdditionalSection::where('user_id', $tenantId)
                ->where('possition', $section)
                ->where('page_type', $pageType)
                ->orderBy('serial_number', 'asc')
                ->get();
        }

        $sectionInfo = BasicSetting::where('user_id', $tenantId)->select('about_additional_section_status')->first();
        if (!empty($sectionInfo->about_additional_section_status)) {
            $info = json_decode($sectionInfo->about_additional_section_status, true);
            $queryResult['aboutSec'] = $info;
        }

        return view('tenant_frontend.about', $queryResult);
    }
}
