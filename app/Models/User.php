<?php

namespace App\Models;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Jobs\UserResetPasswordMail;
use App\Models\BasicSetting as ModelsBasicSetting;
use App\Models\User\Advertisement;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Models\User\CookieAlert;
use App\Models\User\CustomPage\Page;
use App\Models\User\CustomPage\PageContent;
use App\Models\User\FooterQuickLink;
use App\Models\User\FooterText;
use App\Models\User\HomePage\ActionSection;
use App\Models\User\HomePage\NewsletterSection;
use App\Models\User\HomePage\Section;
use App\Models\User\HomePage\SectionTitle;
use App\Models\User\HomePage\Testimonial;
use App\Models\User\HomePage\VideoSection;
use App\Models\User\Journal\Blog;
use App\Models\User\Journal\BlogCategory;
use App\Models\User\Language;
use App\Models\User\Menu;
use App\Models\User\PageHeading;
use App\Models\User\Popup;
use App\Models\User\Social;
use App\Models\User\SEO;
use App\Models\User\FAQ;
use App\Models\User\Follower;
use App\Models\User\HomePage\HomePage;
use App\Models\User\Subscriber;
use App\Models\User\HomePage\Partner;
use App\Models\User\Invoice\Invoice;
use App\Models\User\MailTemplate;
use App\Models\User\Project\Project;
use App\Models\User\Property\Amenity;
use App\Models\User\Property\Category;
use App\Models\User\Property\City;
use App\Models\User\Property\Country;
use App\Models\User\Property\Property;
use App\Models\User\Property\State;
use App\Models\SupportTicket;
use App\Models\User\AdditionalSection;
use App\Models\User\HomePage\AboutSection;
use App\Models\User\Project\Category as ProjectCategory;
use App\Models\User\Project\City as ProjectCity;
use App\Models\User\Project\Contact;
use App\Models\User\Project\Country as ProjectCountry;
use App\Models\User\Project\State as ProjectState;
use App\Models\User\Property\PropertyContact;
use App\Models\User\UserCustomDomain;
use App\Models\User\UserPermission;
use App\Models\User\UserQrCode;
use App\Models\User\UserSubdomain;
use App\Models\User\UserVcard;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'photo',
        'username',
        'password',
        'phone',
        'company_name',
        'city',
        'state',
        'address',
        'country',
        'status',
        'featured',
        'verification_link',
        'email_verified',
        'online_status',
        'show_email_addresss',
        'show_phone_number',
        'show_contact_form',
        'show_profile',
        'show_profile_on_admin_website',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcrypt($value),
        );
    }

    public function register($data)
    {
        
        return self::create([
            'first_name'         => $data['first_name'] ?? null,
            'last_name'          => $data['last_name'] ?? null,
            'email'              => $data['email'],
            'phone'              => $data['phone'] ?? null,
            'username'           => $data['username'],
            'password'           => $data['password'], // Automatically hashed by mutator (attribute)
            'status'             => $data['status'] ?? 0, // default deactive
            'address'            => $data['address'] ?? null,
            'city'               => $data['city'] ?? null,
            'state'              => $data['state'] ?? null,
            'country'            => $data['country'] ?? null,
            'verification_link'  => $data['token'] ?? null,
            'online_status' => $data['online_status'] ?? 1,
            'email_verified' => $data['email_verified'] ?? 0,
        ]);
    }
    public function deleteTenant()
    {
        DB::transaction(function () {
            $tenant = $this;

            $tenant->menus()->delete();
            $languages =  $tenant->languages()->get();

            foreach ($languages as $language) {
                $language->deteleLanguage();
            }

            $basic_setting = $tenant->basic_setting()->get();
            foreach ($basic_setting as $setting) {
                $setting->deleteBasicsetting();
            }

            $tenant->mail_templates()->delete();
            $tenant->social_media()->delete();

            $agents =  $tenant->agents()->get();
            foreach ($agents as $agent) {
                $agent->destroyAgent();
            }

            $projects = $tenant->projects()->get();
            foreach ($projects as $project) {
                $project->destroyProject();
            }
            $projectCities = $tenant->projectCities()->get();
            foreach ($projectCities as $city) {
                $city->deleteCity();
            }
            $projectStates = $tenant->projectStates()->get();
            foreach ($projectStates as $state) {
                $state->deleteState();
            }

            $projectCountries = $tenant->projectCountries()->get();
            foreach ($projectCountries as $country) {
                $country->deleteCountry();
            }

            $projectCategories = $tenant->projectCountries()->get();
            foreach ($projectCategories as $category) {
                $category->deleteCategory();
            }


            $properties = $tenant->properties()->get();
            foreach ($properties as $property) {
                $property->destroyPropertry();
            }

            $cities = $tenant->cities()->get();
            foreach ($cities as $city) {
                $city->deleteCity();
            }

            $states = $tenant->states()->get();
            foreach ($states as $state) {
                $state->deleteState();
            }

            $countries = $tenant->countries()->get();
            foreach ($countries as $country) {
                $country->deleteCountry();
            }
            $amenities = $tenant->amenities()->get();
            foreach ($amenities as $amenity) {
                $amenity->delete();
            }
            $propertyCategories = $tenant->propertyCategories()->get();
            foreach ($propertyCategories as $propertyCategory) {
                $propertyCategory->deleteCategory();
            }

            $tenant->homePage?->deleteHomePage();

            $tenant->home_section()->delete();
            $tenant->customPage->each->deletePage();

            $aditionlSections = $tenant->aditionlSections();
            foreach ($aditionlSections as $sec) {
                $sec->deelete();
            }

            $blogCategories  = $tenant->blog_categories();
            foreach ($blogCategories as $cat) {
                $cat->delete();
            }
            $blogs = $tenant->blogs();
            foreach ($blogs as $blog) {

                @unlink(public_path(Constant::WEBSITE_BLOG_IMAGE . '/' . $blog->image));
                $blog->delete();
            }

            $tenant->following()->delete();
            $tenant->follower()->delete();

            $partners = $tenant->partners()->get();
            foreach ($partners as $partner) {
                @unlink(public_path(Constant::WEBSITE_PARTNERS_IMAGE . '/' . $partner->image));
                $partner->delete();
            }

            $tenant->subscribers()->delete();

            $tenant->advertisements->each->advertisementDelete();

            $tenant->announcementPopups->each->popupDelete();

            /**
             * delete 'online gateways' info
             */
            $online_gateways = $tenant->online_gateways()->get();

            foreach ($online_gateways as $online_gateway) {
                if (!empty($online_gateway)) {
                    $online_gateway->delete();
                }
            }

            /**
             * delete 'offline gateways' info
             */
            $offline_gateways = $tenant->offline_gateways()->get();

            foreach ($offline_gateways as $offline_gateway) {
                if (!empty($offline_gateway)) {
                    $offline_gateway->delete();
                }
            }
            /**
             * delete 'custom domains' info
             */
            $custom_domains = $tenant->custom_domains()->get();
            if ($custom_domains->count() > 0) {
                foreach ($custom_domains as $custom_domain) {
                    $custom_domain->delete();
                }
            }


            /**
             * delete 'sub domains' info
             */
            $custom_domains = $tenant->subdomains()->get();
            if ($custom_domains->count() > 0) {
                foreach ($custom_domains as $custom_domain) {
                    $custom_domain->delete();
                }
            }

            /**
             * delete 'memberships' info
             */
            $memberships = $tenant->memberships()->get();
            if ($memberships->count() > 0) {
                foreach ($memberships as $membership) {
                    @unlink(public_path('assets/front/img/membership/receipt/' . $membership->receipt));
                    $membership->delete();
                }
            }

            /**
             * permission info delete
             */
            if ($tenant->permission) {
                $tenant->permission->delete();
            }

            /**
             * customers info delete
             */
            $customers = $tenant->customers();
            foreach ($customers as $customer) {
                $customer->customerDelete();
            }

            /**
             * delete support tikets 
             */
            $supportTickets  =  $tenant->user_tickets();
            foreach ($supportTickets as $tickets) {
                $tickets->deleteTicket();
            }



            //user profile image
            @unlink(public_path(Constant::USER_IMAGE . '/' . $tenant->photo));
            $tenant->delete();
        });
    }

    public function photo(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !is_null($value) ? (Constant::USER_IMAGE . '/' . $value) : 'assets/img/blank-user.jpg'
        );
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => ucwords(trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')))
        );
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id', 'id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id', 'id');
    }

    public function featuredProperties()
    {
        return $this->hasMany(Property::class, 'user_id', 'id')->where('featured', 1);
    }

    public function projectCities(): HasMany
    {
        return $this->hasMany(ProjectCity::class, 'user_id');
    }
    public function projectStates(): HasMany
    {
        return $this->hasMany(ProjectState::class, 'user_id');
    }
    public function projectCountries(): HasMany
    {
        return $this->hasMany(ProjectCountry::class, 'user_id');
    }
    public function projectCategories(): HasMany
    {
        return $this->hasMany(ProjectCategory::class, 'user_id');
    }
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'user_id');
    }
    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'user_id');
    }
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class, 'user_id');
    }
    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class, 'user_id');
    }

    public function propertyCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'user_id');
    }
    public function homePage(): HasOne
    {
        return $this->hasOne(HomePage::class, 'user_id');
    }
    public function home_section(): HasOne
    {
        return $this->hasOne(Section::class, 'user_id');
    }
    public function aditionlSections(): HasMany
    {
        return $this->hasMany(AdditionalSection::class, 'user_id');
    }

    public function customPage(): HasMany
    {
        return $this->hasMany(Page::class, 'user_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'user_id');
    }
    public function faqs(): HasMany
    {
        return $this->hasMany(FAQ::class, 'user_id');
    }


    public function follower(): HasMany
    {

        return $this->hasMany(Follower::class, 'following_id', 'id');
    }

    public function following(): HasMany
    {

        return $this->hasMany(Follower::class, 'follower_id', 'id');
    }
    public function subscribers()
    {
        return $this->hasMany(Subscriber::class, 'user_id');
    }
    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class, 'user_id');
    }
    public function announcementPopups(): HasMany
    {
        return $this->hasMany(Popup::class, 'user_id');
    }
    public function subdomains(): HasMany
    {
        return $this->hasMany(UserSubdomain::class, 'user_id', 'id');
    }
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'user_id', 'id');
    }
    public function partners()
    {
        return $this->hasMany(Partner::class, 'user_id', 'id');
    }
    public function user_tickets()
    {
        return $this->hasMany(SupportTicket::class, 'user_id', 'id');
    }













    public function user_custom_domains()
    {
        return $this->hasMany(UserCustomDomain::class, 'user_id');
    }

    public function page_heading(): HasOne
    {
        return $this->hasOne(PageHeading::class, 'user_id');
    }
    public function custom_domains(): HasMany
    {
        return $this->hasMany(UserCustomDomain::class);
    }
    public function mail_templates(): HasMany
    {
        return $this->hasMany(MailTemplate::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'user_id');
    }

    public function basic_setting(): HasOne
    {
        return $this->hasOne(BasicSetting::class, 'user_id');
    }




    public function seos(): HasMany
    {
        return $this->hasMany(SEO::class, 'user_id');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class, 'user_id');
    }



    public function blog_categories(): HasMany
    {
        return $this->hasMany(BlogCategory::class, 'user_id');
    }

    public function social_media(): HasMany
    {
        return $this->hasMany(Social::class, 'user_id');
    }
    public function allLanguages()
    {
        return $this->hasMany(Language::class, 'user_id');
    }
    public function languages()
    {
        return $this->hasMany(Language::class, 'user_id');
    }

    public function footer_quick_links()
    {
        return $this->hasMany(FooterQuickLink::class, 'user_id');
    }


    public function footer_texts(): HasMany
    {
        return $this->hasMany(FooterText::class, 'user_id');
    }


    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'user_id');
    }





    public function sectionTitle(): HasOne
    {
        return $this->hasOne(SectionTitle::class, 'user_id');
    }



    public function videoSection(): HasOne
    {
        return $this->hasOne(VideoSection::class, 'user_id');
    }

    public function customPageInfo(): HasMany
    {
        return $this->hasMany(PageContent::class, 'user_id');
    }


    public function newsletterSection(): HasOne
    {
        return $this->hasOne(NewsletterSection::class, 'user_id');
    }

    public function cookieAlertInfo(): HasOne
    {
        return $this->hasOne(CookieAlert::class, 'user_id');
    }

    public function about_us_section(): HasMany
    {
        return $this->hasMany(AboutSection::class, 'user_id');
    }


    public function online_gateways(): HasMany
    {
        return $this->hasMany(\App\Models\User\PaymentGateway::class, 'user_id');
    }
    public function offline_gateways(): HasMany
    {
        return $this->hasMany(\App\Models\User\OfflineGateway::class, 'user_id');
    }
    public function actionSection(): HasOne
    {
        return $this->hasOne(ActionSection::class, 'user_id');
    }
    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {

        $user  = User::where('email', request()->email)->first();

        UserResetPasswordMail::dispatch($user, $token);
        session()->flash('success', "we sent you an email. Please check your inbox");
    }






    public function allCustomers()
    {
        return $this->hasMany(Customer::class, 'user_id', 'id');
    }



    public function allPosts()
    {
        return $this->hasMany(Blog::class, 'user_id', 'id');
    }
    public function posts()
    {
        return $this->hasMany(Blog::class, 'user_id', 'id');
    }






    public function permission()
    {
        return $this->hasOne(UserPermission::class, 'user_id', 'id');
    }



    public function agents()
    {
        return $this->hasMany(Agent::class, 'user_id', 'id');
    }

    public function propertyContact()
    {
        return $this->hasMany(PropertyContact::class, 'user_id', 'id');
    }
    public function projectContact()
    {
        return $this->hasMany(Contact::class, 'user_id', 'id');
    }
}
