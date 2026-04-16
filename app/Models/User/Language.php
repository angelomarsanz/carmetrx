<?php

namespace App\Models\User;

use App\Models\User\Agent\AgentInfo;
use App\Models\User\CustomPage\PageContent;
use App\Models\User\HomePage\AboutSection;
use App\Models\User\HomePage\CounterInformation;
use App\Models\User\HomePage\HeroSlider;
use App\Models\User\HomePage\HeroStatic;
use App\Models\User\HomePage\NewsletterSection;
use App\Models\User\HomePage\SectionTitle;
use App\Models\User\HomePage\Testimonial;
use App\Models\User\HomePage\TestimonialSection;
use App\Models\User\HomePage\VideoSection;
use App\Models\User\HomePage\WhyChooseUsSection;
use App\Models\User\HomePage\WorkProcess;
use App\Models\User\Journal\BlogCategoryContent;
use App\Models\User\Journal\BlogInformation;
use App\Models\User\Project\CategoryContent as ProjectCategoryContent;
use App\Models\User\Project\CityContent as ProjectCityContent;
use App\Models\User\Project\CountryContent as ProjectCountryContent;
use App\Models\User\Project\ProjectContent;
use App\Models\User\Project\ProjectSpecificationContent;
use App\Models\User\Project\ProjectTypeContent;
use App\Models\User\Project\StateContent as ProjectStateContent;
use App\Models\User\Property\AmenityContent;
use App\Models\User\Property\CategoryContent;
use App\Models\User\Property\CityContent;
use App\Models\User\Property\CountryContent;
use App\Models\User\Property\PropertyContent;
use App\Models\User\Property\PropertySpecificationContent;
use App\Models\User\Property\StateContent;
use App\Models\User\SEO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Language extends Model
{
    public $table = "user_languages";

    protected $fillable = [
        'id',
        'name',
        'is_default',
        'code',
        'rtl',
        'user_id',
        'is_admin',
        'keywords'
    ];

    public function store($tenantId, $data)
    {

        return self::create([
            'user_id' => $tenantId,
            'name' => $data['name'],
            'code' => $data['code'],
            'is_default' => $data['is_default'],
            'rtl' => $data['rtl'],
            'is_admin' => 0,
            'keywords' => $data['keywords']
        ]);
    }


    public function deteleLanguage()
    {
        DB::transaction(function () {
            $language = $this;
            $language->videoSection()->delete();
            $language->aboutSection()->delete();
            $language->cookieAlertInfo()->delete();
            $language->counterInfos()->delete();
            $language->faqs()->delete();
            $language->footerQuickLinks()->delete();
            $language->footerText()->delete();
            $sliders = $language->heroSlider()->get();
            foreach ($sliders as $slider) {
                $slider->deleteSlider();
            }

            $language->heroStatic()->delete();
            $language->menus()->delete();
            $language->newsletterSection()->delete();
            $language->page_contents()->delete();
            $language->pageName()->delete();
            $language->sectionTitle()->delete();
            $language->seos()->delete();
            $testimonials = $language->testimonials()->get();
            foreach ($testimonials as $testimonial) {
                $testimonial->deleteTestimonial();
            }
            $announcementPopups = $language->announcementPopups()->get();
            foreach ($announcementPopups as $announcementPopup) {
                $announcementPopup->popupDelete();
            }
            // $language->announcementPopups()->popupDelete();
            $language->testimonialSecInfo()->delete();
            $language->whyChooseUsSection()->delete();
            $language->workSteps()->delete();
            $language->blogInformations()->delete();
            $language->blogCategoryContents()->delete();
            $language->countryContents()->delete();
            $language->stateContents()->delete();
            $language->cityContents()->delete();
            $language->amenityContents()->delete();
            $language->propertyCategoryContents()->delete();
            $language->propertySpeciContents()->delete();
            $language->propertyContents()->delete();
            $language->projectSpeciContents()->delete();
            $language->projectTypeContents()->delete();
            $language->projectCountryContents()->delete();
            $language->projectStateContents()->delete();
            $language->projectCityContents()->delete();
            $language->projectCategoryContents()->delete();
            $language->projectContents()->delete();
            $language->agentInfos()->delete();
            $language->additionalSecContents()->delete();
            // ===== language delete =====
            $language->delete();
        });
    }

    public function additionalSecContents()
    {
        return $this->hasMany(AdditionalSectionContent::class, 'language_id', 'id');
    }

    public function aboutSection()
    {
        return $this->hasOne(AboutSection::class, 'language_id', 'id');
    }
    public function videoSection(): HasOne
    {
        return $this->hasOne(VideoSection::class, 'language_id');
    }
    public function cookieAlertInfo()
    {
        return $this->hasOne(CookieAlert::class, 'language_id', 'id');
    }
    public function counterInfos()
    {
        return $this->hasMany(CounterInformation::class, 'language_id', 'id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(FAQ::class, 'language_id', 'id');
    }
    public function footerQuickLinks()
    {
        return $this->hasMany(FooterQuickLink::class, 'language_id', 'id');
    }
    public function footerText()
    {
        return $this->hasOne(FooterText::class, 'language_id', 'id');
    }
    public function heroSlider()
    {
        return $this->hasMany(HeroSlider::class, 'language_id', 'id');
    }
    public function heroStatic()
    {
        return $this->hasOne(HeroStatic::class, 'language_id', 'id');
    }
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'language_id');
    }
    public function newsletterSection(): HasOne
    {
        return $this->hasOne(NewsletterSection::class, 'language_id');
    }
    public function page_contents(): HasMany
    {
        return $this->hasMany(PageContent::class, 'language_id');
    }
    public function pageName(): HasOne
    {
        return $this->hasOne(PageHeading::class, 'language_id');
    }
    public function sectionTitle(): HasOne
    {
        return $this->hasOne(SectionTitle::class, 'language_id');
    }
    public function seos(): HasOne
    {
        return $this->hasOne(SEO::class, 'language_id');
    }
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class, 'language_id');
    }
    public function testimonialSecInfo(): HasOne
    {
        return $this->hasOne(TestimonialSection::class, 'language_id');
    }
    public function whyChooseUsSection(): HasOne
    {
        return $this->hasOne(WhyChooseUsSection::class, 'language_id', 'id');
    }
    public function workSteps(): HasMany
    {
        return $this->hasMany(WorkProcess::class, 'language_id');
    }
    public function blogInformations(): HasMany
    {
        return $this->hasMany(BlogInformation::class, 'language_id');
    }

    public function blogCategoryContents(): HasMany
    {
        return $this->hasMany(BlogCategoryContent::class, 'language_id', 'id');
    }
    public function countryContents(): HasMany
    {
        return $this->hasMany(CountryContent::class, 'language_id', 'id');
    }
    public function projectCountryContents(): HasMany
    {
        return $this->hasMany(ProjectCountryContent::class, 'language_id', 'id');
    }
    public function stateContents(): HasMany
    {
        return $this->hasMany(StateContent::class, 'language_id', 'id');
    }
    public function projectStateContents(): HasMany
    {
        return $this->hasMany(ProjectStateContent::class, 'language_id', 'id');
    }
    public function cityContents(): HasMany
    {
        return $this->hasMany(CityContent::class, 'language_id', 'id');
    }
    public function projectCityContents(): HasMany
    {
        return $this->hasMany(ProjectCityContent::class, 'language_id', 'id');
    }
    public function amenityContents(): HasMany
    {
        return $this->hasMany(AmenityContent::class, 'language_id', 'id');
    }
    public function propertyCategoryContents(): HasMany
    {
        return $this->hasMany(CategoryContent::class, 'language_id', 'id');
    }
    public function projectCategoryContents(): HasMany
    {
        return $this->hasMany(ProjectCategoryContent::class, 'language_id', 'id');
    }
    public function propertySpeciContents(): HasMany
    {
        return $this->hasMany(PropertySpecificationContent::class, 'language_id', 'id');
    }
    public function propertyContents(): HasMany
    {
        return $this->hasMany(PropertyContent::class, 'language_id', 'id');
    }

    public function projectSpeciContents(): HasMany
    {
        return $this->hasMany(ProjectSpecificationContent::class, 'language_id', 'id');
    }
    public function projectContents(): HasMany
    {
        return $this->hasMany(ProjectContent::class, 'language_id', 'id');
    }
    public function projectTypeContents(): HasMany
    {
        return $this->hasMany(ProjectTypeContent::class, 'language_id', 'id');
    }

    public function agentInfos(): HasMany
    {
        return $this->hasMany(AgentInfo::class, 'language_id', 'id');
    }
    public function announcementPopups(): HasMany
    {
        return $this->hasMany(Popup::class, 'language_id', 'id');
    }


    public function seoInfo()
    {
        return $this->hasOne(SEO::class, 'language_id', 'id');
    }






















    // public function form()
    // {
    //     return $this->hasMany(Form::class, 'language_id', 'id');
    // }






















    public function postInformation()
    {
        return $this->hasMany(BlogInformation::class, 'language_id', 'id');
    }


    // public function footer_texts()
    // {
    //     return $this->hasMany(FooterText::class, 'language_id', 'id');
    // }

    // public function quick_links()
    // {
    //     return $this->hasMany(FooterQuickLink::class, 'language_id', 'id');
    // }






    public function featuredSecInfo()
    {
        return $this->sectionTitle()->select('id', 'featured_property_section_title');
    }
    public function agentSecInfo()
    {
        return $this->sectionTitle()->select('id', 'agent_section_title', 'agent_section_subtitle');
    }
    public function categorySecInfo()
    {
        return $this->sectionTitle()->select('id', 'category_section_title', 'category_section_subtitle');
    }
    public function propertySecInfo()
    {
        return $this->sectionTitle()->select('id', 'property_section_title');
    }
    public function projectSecInfo()
    {
        return $this->sectionTitle()->select('id', 'project_section_title', 'project_section_subtitle');
    }
    public function workStepsSecInfo()
    {
        return $this->sectionTitle()->select('id', 'work_process_title', 'work_process_subtitle');
    }
}
