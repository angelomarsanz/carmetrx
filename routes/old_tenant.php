<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\FaqController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\AgentController;
use App\Http\Controllers\User\BasicController;
use App\Http\Controllers\User\PopupController;
use App\Http\Controllers\User\DomainController;
use App\Http\Controllers\User\FooterController;
use App\Http\Controllers\User\SocialController;
use App\Http\Controllers\Payment\YocoController;
use App\Http\Controllers\User\BuyPlanController;
use App\Http\Controllers\User\FollowerController;
use App\Http\Controllers\User\LanguageController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Payment\XenditController;
use App\Http\Controllers\User\AboutPageController;
use App\Http\Controllers\User\SubdomainController;
use App\Http\Controllers\User\CustomPageController;
use App\Http\Controllers\User\PaymentLogController;
use App\Http\Controllers\User\SubscriberController;
use App\Http\Controllers\Payment\MidtransController;
use App\Http\Controllers\User\CookieAlertController;
use App\Http\Controllers\User\MenuBuilderController;
use App\Http\Controllers\User\PageHeadingController;
use App\Http\Controllers\Payment\ToyyibpayController;
use App\Http\Controllers\User\Journal\BlogController;
use App\Http\Controllers\User\MailTemplateController;
use App\Http\Controllers\User\AdvertisementController;
use App\Http\Controllers\User\HomePage\HeroController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\HomePage\AboutController;
use App\Http\Controllers\User\RegisteredUserController;
use App\Http\Controllers\Payment\PerfectMoneyController;
use App\Http\Controllers\User\HomePage\CounterController;
use App\Http\Controllers\User\Homepage\PartnerController;
use App\Http\Controllers\User\HomePage\SectionController;
use App\Http\Controllers\User\Journal\CategoryController;
use App\Http\Controllers\User\AdditionalSectionController;
use App\Http\Controllers\User\HomePage\TestimonialController;
use App\Http\Controllers\User\HomePage\WhyChooseUsController;
use App\Http\Controllers\User\HomePage\WorkProcessController;
use App\Http\Controllers\User\HomePage\SectionTitleController;
use App\Http\Controllers\User\AboutAdditionalSectionController;
use App\Http\Controllers\User\ProjectManagement\TypeController;
use App\Http\Controllers\User\PropertyManagement\CityController;
use App\Http\Controllers\User\PropertyManagement\StateController;
use App\Http\Controllers\User\ProjectManagement\ProjectController;
use App\Http\Controllers\User\PropertyManagement\AmenityController;
use App\Http\Controllers\User\PropertyManagement\CountryController;
use App\Http\Controllers\User\PropertyManagement\MessageController;
use App\Http\Controllers\User\PropertyManagement\PropertyController;
use App\Http\Controllers\User\ProjectManagement\CityController as ProjectCityController;
use App\Http\Controllers\User\ProjectManagement\StateController as ProjectStateController;
use App\Http\Controllers\User\ProjectManagement\CountryController as ProjectCountryController;
use App\Http\Controllers\User\ProjectManagement\CategoryController as ProjectCategoryController;
use App\Http\Controllers\User\PropertyManagement\CategoryController as PropertyCategoryController;


/*=======================================================
    ******************** User Routes **********************
    =======================================================*/

Route::get('/myfatoorah/callback', 'MyFatoorahController@callback')->name('myfatoorah.success');
Route::get('myfatoorah/cancel', 'MyFatoorahController@cancel')->name('myfatoorah.cancel');

Route::post('generate-prompt', 'AIContentController@generatePrompt')->name('generate.prompt');
Route::get('generate-content', 'AIContentController@generateContent')->name('generate.content');


Route::get('/midtrans/bank-notify', 'MidtransBankNotifyController@bank_notify')->name('midtrans.bank_notify');
Route::get('/midtrans/cancel', 'MidtransBankNotifyController@cancel')->name('midtrans.cancel');

Route::group(['prefix' => 'user', 'middleware' => ['auth:web', 'userstatus', 'TenantDashboardLang']], function () {

    //property management
    Route::prefix('property-management')->middleware('checkUserPermission:Property Management')->group(function () {
        // property category route
        // Route::controller(PropertyCategoryController::class)->group(function () {

        //     Route::get('/categories', 'index')->name('user.property_management.categories');
        //     Route::post('/store-category', 'store')->name('user.property_management.store_category');
        //     Route::post('/update-category', 'update')->name('user.property_management.update_category');
        //     Route::post('/update-category-featured', 'updateFeatured')->name('user.property_management.update_category_featured');
        //     Route::post('/delete-category', 'destroy')->name('user.property_management.delete_category');
        //     Route::post('/bulk-delete-category', 'bulkDestroy')->name('user.property_management.bulk_delete_category');
        // });
        // property Amenities route
        // Route::controller(AmenityController::class)->group(function () {
        //     Route::get('/amenity', 'index')->name('user.property_management.amenities');
        //     Route::post('/store-amenity', 'store')->name('user.property_management.store_amenity');
        //     Route::post('/update-amenity', 'update')->name('user.property_management.update_amenity');
        //     Route::post('/delete-amenity', 'destroy')->name('user.property_management.delete_amenity');
        //     Route::post('/bulk-delete-amenity', 'bulkDestroy')->name('user.property_management.bulk_delete_amenity');
        // });
        // property countries route
        // Route::controller(CountryController::class)->group(function () {
        //     Route::get('/country', 'index')->name('user.property_management.countries');
        //     Route::post('/store-country', 'store')->name('user.property_management.store_country');
        //     Route::post('/update-country', 'update')->name('user.property_management.update_country');
        //     Route::post('/delete-country', 'destroy')->name('user.property_management.delete_country');
        //     Route::post('/bulk-delete-country', 'bulkDestroy')->name('user.property_management.bulk_delete_country');
        // });
        // property countries route
        Route::controller(StateController::class)->group(function () {
            //     Route::get('/states', 'index')->name('user.property_management.states');
            //     Route::get('/get-state', 'getState')->name('user.property_management.get_state');
            Route::get('/get-states-cities', 'getStateCities')->name('user.property_management.get_state_cities');
            //     Route::post('/store-state', 'store')->name('user.property_management.store_state');
            //     Route::post('/update-state', 'update')->name('user.property_management.update_state');
            //     Route::post('/delete-state', 'destroy')->name('user.property_management.delete_state');
            //     Route::post('/bulk-delete-state', 'bulkDestroy')->name('user.property_management.bulk_delete_state');
        });
        // // property cities route
        Route::controller(CityController::class)->group(function () {
        //     Route::get('/cities', 'index')->name('user.property_management.cities');
            Route::get('/get-cities', 'getCities')->name('user.property_management.get_cities');
        //     Route::post('/store-city', 'store')->name('user.property_management.store_city');
        //     Route::post('/update-city', 'update')->name('user.property_management.update_city');
        //     Route::post('/update-featured', 'updateFeatured')->name('user.property_management.update_city_featured');
        //     Route::post('/delete-city', 'destroy')->name('user.property_management.delete_city');
        //     Route::post(
        //         '/bulk-delete-city',
        //         'bulkDestroy'
        //     )->name('user.property_management.bulk_delete_city');
        });

        Route::controller(PropertyController::class)->group(function () {
            Route::get('/settings', 'settings')->name('user.property_management.settings');
            Route::post('/update-settings', 'update_settings')->name('user.property_management.update_settings');
            Route::get('/properties', 'index')->name('user.property_management.properties');
            Route::get('/type', 'type')->name('user.property_management.type');
            Route::get('/create', 'create')->name('user.property_management.create_property');
            // Route::get('/get-agent', 'getAgent')->name('user.property_management.get_agent');
            Route::post('/store', 'store')->name('user.property_management.store_property')->middleware('packageLimitsCheck:property,store');
            Route::post('/update_featured', 'updateFeatured')->name('user.property_management.update_featured')->middleware('packageLimitsCheck:property,update');
            Route::post('update_status', 'updateStatus')->name('user.property_management.update_status');
            Route::get('edit-property/{id}', 'edit')->name('user.property_management.edit');
            Route::post('update/{id}', 'update')->name('user.property_management.update_property')->middleware('packageLimitsCheck:property,update');
            Route::post('specification/delete', 'specificationDelete')->name('user.property_management.specification_delete');
            Route::post('/featured-payment', 'featuredPayment')->name('user.property_management.featured_payment');
            //#========== Property slider image
            Route::post('/img-store', 'imagesstore')->name('user.property.imagesstore')->middleware('packageLimitsCheck:property,store');
            Route::post('/img-update', 'imagesstore')->name('user.property.imagesupdate')->middleware('packageLimitsCheck:property,store');
            Route::post('/img-remove', 'imagermv')->name('user.property.imagermv');
            Route::post('/img-db-remove', 'imagedbrmv')->name('user.property.imgdbrmv');
            //#==========property slider image end
            Route::post('delete', 'delete')->name('user.property_management.delete_property');
            Route::post('bulk-delete', 'bulkDelete')->name('user.property_management.bulk_delete_property');

            Route::post('/property/video-img-rmv', 'videoImgrmv')->name('user.property_management.videoImgrmv');
            Route::post('/property/floor-img-rmv', 'floorImgrmv')->name('user.property_management.floorImgrmv');
        });

        Route::get('/messages', [MessageController::class, 'index'])->name('user.property_management.messages');
        Route::post('/message-delete', [MessageController::class, 'destroy'])->name('user.property_management.message.destroy');
    });

    Route::prefix('agent-management')->middleware('checkUserPermission:Agent')->controller(AgentController::class)->group(function () {
        Route::get('/', 'index')->name('user.agent_management.index');
        Route::post('/store', 'store')->name('user.agent_management.register')->middleware('packageLimitsCheck:agent,store');
        Route::post('/update', 'update')->name('user.agent_management.update_agent')->middleware('packageLimitsCheck:agent,store');
        Route::post('/update-status/{id}', 'changeStatus')->name('user.agent_management.change_status')->middleware('packageLimitsCheck:agent,update');
        Route::get('/secret-login/{id}', 'secret_login')->name('user.agent_management.secret_login');
        Route::post('/{id}/delete', 'destroy')->name('user.agent_management.destroy');
    });


    // Project Management route start
    Route::prefix('project-management')->middleware('checkUserPermission:Project Management')->group(function () {

        // Project category route
        Route::controller(ProjectCategoryController::class)->group(function () {

            Route::get('/categories', 'index')->name('user.project_management.categories');
            Route::post('/store-category', 'store')->name('user.project_management.store_category');
            Route::post('/update-category', 'update')->name('user.project_management.update_category');
            Route::post('/update-category-featured', 'updateFeatured')->name('user.project_management.update_category_featured');
            Route::post('/delete-category', 'destroy')->name('user.project_management.delete_category');
            Route::post('/bulk-delete-category', 'bulkDestroy')->name('user.project_management.bulk_delete_category');
        });

        // Project countries route
        Route::controller(ProjectCountryController::class)->group(function () {
            Route::get('/country', 'index')->name('user.project_management.countries');
            Route::post('/store-country', 'store')->name('user.project_management.store_country');
            Route::post('/update-country', 'update')->name('user.project_management.update_country');
            Route::post('/delete-country', 'destroy')->name('user.project_management.delete_country');
            Route::post('/bulk-delete-country', 'bulkDestroy')->name('user.project_management.bulk_delete_country');
        });

        // Project countries route
        Route::controller(ProjectStateController::class)->group(function () {
            Route::get('/states', 'index')->name('user.project_management.states');
            Route::get('/get-state', 'getState')->name('user.project_management.get_state');
            Route::get('/get-states-cities', 'getStateCities')->name('user.project_management.get_state_cities');
            Route::post('/store-state', 'store')->name('user.project_management.store_state');
            Route::post('/update-state', 'update')->name('user.project_management.update_state');
            Route::post('/delete-state', 'destroy')->name('user.project_management.delete_state');
            Route::post('/bulk-delete-state', 'bulkDestroy')->name('user.project_management.bulk_delete_state');
        });

        // Project cities route
        Route::controller(ProjectCityController::class)->group(function () {
            Route::get('/cities', 'index')->name('user.project_management.cities');
            Route::get('/get-cities', 'getCities')->name('user.project_management.get_cities');
            Route::post('/store-city', 'store')->name('user.project_management.store_city');
            Route::post('/update-city', 'update')->name('user.project_management.update_city');
            Route::post('/update-featured', 'updateFeatured')->name('user.project_management.update_city_featured');
            Route::post('/delete-city', 'destroy')->name('user.project_management.delete_city');
            Route::post('/bulk-delete-city', 'bulkDestroy')->name('user.project_management.bulk_delete_city');
        });

        Route::controller(ProjectController::class)->group(function () {
            Route::get('/settings', 'settings')->name('user.project_management.settings');
            Route::post('/update-settings', 'updateSettings')->name('user.project_management.update_settings');
            Route::get('/projects', 'index')->name('user.project_management.projects');
            Route::get('/create', 'create')->name('user.project_management.create_project');
            Route::post('/store', 'store')->name('user.project_management.store_project')->middleware('packageLimitsCheck:project,store');
            Route::post('/update_featured', 'updateFeatured')->name('user.project_management.update_featured');
            Route::post('update_status', 'updateStatus')->name('user.project_management.update_status');
            Route::get('edit-project/{id}', 'edit')->name('user.project_management.edit');
            Route::post('update/{id}', 'update')->name('user.project_management.update_project')->middleware('packageLimitsCheck:project,update');
            Route::post('specification/delete', 'specificationDelete')->name('user.project_management.specification_delete');
            Route::post('/delete', 'destroy')->name('user.project_management.delete_project');
            Route::post('/bulk-delete', 'bulkDestroy')->name('user.project_management.bulk_delete_project');
            //#========== project gallery image
            Route::post('/gallery-img-store', 'galleryImagesStore')->name('user.project.gallery_image_store')->middleware('packageLimitsCheck:project,update');
            Route::post('/img-remove', 'galleryImageRmv')->name('user.project.gallery_imagermv');
            Route::post('/img-db-remove', 'galleryImageDbrmv')->name('user.project.gallery_imgdbrmv');
            //#========== project slider image end
            //#========== project gallery image
            Route::post('/floor-plan-img-store', 'floorPlanImagesStore')->name('user.project.floor_plan_image_store');
            Route::post('/floor-plan-img-remove', 'floorPlanImageRmv')->name('user.project.floor_plan_imagermv');
            Route::post('/floor-plan-img-db-remove', 'floorPlanImageDbrmv')->name('user.project.floor_plan_imgdbrmv');
            //#========== project slider image end

            Route::get('/messages',  'messages')->name('user.project_management.messages');
            Route::post('/message-delete',  'messageDestroy')->name('user.project_management.message.destroy');
        });
        // Project type routes
        Route::prefix('type')->controller(TypeController::class)->group(function () {
            Route::get('/{id}', 'index')->name('user.project_management.project_types');
            Route::post('/store', 'store')->name('user.project_management.project_type.store')->middleware('packageLimitsCheck:projectType,store');
            Route::post('/update', 'update')->name('user.project_management.project_type.update')->middleware('packageLimitsCheck:projectType,update');
            Route::post('/delete', 'delete')->name('user.project_management.delete_type');
            Route::post('/bulk-delete', 'bulkDelete')->name('user.project_management.bulk_delete_type');
        });
    });
    // Project Management Route End


    // admin to usersupport tickets route

    Route::prefix('support-tickets')->controller(SupportTicketController::class)->group(function () {
        Route::get('/', 'userTickets')->name('admin_user.support_tickets');

        Route::get('/create-ticket', 'userCreateTicket')->name('admin_user.support_tickets.create');

        Route::post('/store-temp-file', 'userStoreTempFile')->name('admin_user.support_tickets.store_temp_file');

        Route::post('/store-ticket', 'userStoreTicket')->name('admin_user.support_tickets.store');
    });
    Route::controller(SupportTicketController::class)->group(function () {
        Route::get('/support-ticket/{id}/conversation', 'userConversation')->name('admin_user.support_ticket.conversation');
        Route::post('/support-ticket/{id}/reply', 'userTicketReply')->name('admin_user.support_ticket.reply');
        Route::post('/support-ticket/{id}/delete', 'userDestroy')->name('admin_user.support_ticket.delete');
        Route::post('/support-ticket/bulk-delete', 'userBulkDestroy')->name('admin_user.support_tickets.bulk_delete');
    });
    // admin to user support ticket

    // home-page route start

    Route::prefix('/home-page')->group(function () {
        // hero section
        Route::prefix('/hero-section')->controller(HeroController::class)->group(function () {
            Route::get('', 'index')->name('user.home_page.hero_section');
            Route::post('/store-slider', 'storeSlider')->name('user.home_page.store_slider');
            Route::post('/update-slider', 'updateSlider')->name('user.home_page.update_slider');
            Route::post('/delete-slider/{id}', 'destroySlider')->name('user.home_page.delete_slider');
            Route::post('/update-image', 'updateImg')->name('user.home_page.update_hero_img');
            Route::post('/update-info', 'updateHeroInfo')->name('user.home_page.update_hero_info');
        });


        Route::controller(SectionTitleController::class)->group(function () {

            Route::get('/images-&-texts', 'imagesTexts')->name('user.home_page.images_&_texts');
            Route::post('/images-&-texts', 'updateImagesTexts')->name('user.home_page.update_images_&_texts');
        });
        Route::prefix('about-page')->group(function () {

            //additional section
            Route::prefix('additional-sections')->controller(AboutAdditionalSectionController::class)->group(function () {
                Route::get('sections', 'index')->name('user.about.additional_sections');
                Route::get('add-section', 'create')->name('user.about.additional_section.create');
                Route::post('store-section', 'store')->name('user.about.additional_section.store');
                Route::get('edit-section/{id}', 'edit')->name('user.about.additional_section.edit');
                Route::post('update/{id}', 'update')->name('user.about.additional_section.update');
                Route::post('delete/{id}', 'delete')->name('user.about.additional_section.delete');
                Route::post('bulkdelete', 'bulkdelete')->name('user.about.additional_section.bulkdelete');
            });

            // sections show hide
            Route::get('/sections', [AboutPageController::class, 'sections'])->name('user.about.sections.index');
            Route::post('/sections/update', [AboutPageController::class, 'updatesections'])->name('user.about.sections.update');
        });


        Route::prefix('additional-sections')->controller(AdditionalSectionController::class)->group(function () {
            Route::get('sections', 'index')->name('user.additional_sections');
            Route::get('add-section', 'create')->name('user.additional_section.create');
            Route::post('store-section', 'store')->name('user.additional_section.store');
            Route::get('edit-section/{id}', 'edit')->name('user.additional_section.edit');
            Route::post('update/{id}', 'update')->name('user.additional_section.update');
            Route::post('delete/{id}', 'delete')->name('user.additional_section.delete');
            Route::post('bulkdelete', 'bulkdelete')->name('user.additional_section.bulkdelete');
        });

        // about section
        Route::prefix('/about-section')->controller(AboutController::class)->group(function () {
            Route::get('/', 'index')->name('user.home_page.about_section');
            Route::post('/update-image', 'updateImage')->name('user.home_page.update_about_img');
            Route::post('/update-info', 'updateInfo')->name('user.home_page.update_about_info');
        });


        // why choose us section
        Route::prefix('/why-choose-us-section')->controller(WhyChooseUsController::class)->group(function () {
            Route::get('/', 'index')->name('user.home_page.why_choose_us_section');
            Route::post('/update-image', 'updateImage')->name('user.home_page.update_why_choose_us_img');
            Route::post('/update-info/{language}', 'updateInfo')->name('user.home_page.update_why_choose_us_info');
        });


        // counter  section
        Route::controller(CounterController::class)->group(function () {
            Route::get('/counter-section', 'index')->name('user.home_page.counter_section');
            Route::post('/store-counter', 'store')->name('user.home_page.store_counter');
            // Route::post('/counter-bg-img', 'updateBgImg')->name('user.home_page.update_counter_bg');
            Route::post('/update-counter-section', 'update')->name('user.home_page.update_counter');
            Route::post('{id}/delete', 'destroy')->name('user.home_page.delete_counter');
            Route::post('/bulk-delete', 'bulkDestroy')->name('user.home_page.bulk_delete_counter');
        });
        // work process  section
        Route::prefix('/work-steps-section')->controller(WorkProcessController::class)->group(function () {
            Route::get('/', 'index')->name('user.home_page.work_process_section');
            Route::post('/update-background-image', 'updateBgImg')->name('user.home_page.update_work_process_bg');
            Route::post('/info', 'updateSectionInfo')->name('user.home_page.update_work_process_section_info');
            Route::post('/store-work-steps', 'store')->name('user.home_page.store_work_process');
            Route::post('/update-work-steps', 'update')->name('user.home_page.update_work_process');
            Route::post('/delete-work-steps/{id}', 'destroy')->name('user.home_page.delete_work_process');
            Route::post('/bulk-delete-work-steps', 'bulkDestroy')->name('user.home_page.bulk_delete_work_process');
        });

        // testimonials section
        Route::prefix('/testimonials-section')->controller(TestimonialController::class)->group(function () {
            Route::get('/', 'index')->name('user.home_page.testimonials_section');
            Route::post('/update-background-image', 'updateBgImg')->name('user.home_page.update_testimonials_bg');
            Route::post('/testimonial-section-info', 'updateSectionInfo')->name('user.home_page.update_testimonial_section_info');
            Route::post('/store-testimonial', 'storeTestimonial')->name('user.home_page.store_testimonial');
            Route::post('/update-testimonial', 'updateTestimonial')->name('user.home_page.update_testimonial');
            Route::post('/delete-testimonial/{id}', 'destroyTestimonial')->name('user.home_page.delete_testimonial');
            Route::post('/bulk-delete-testimonial', 'bulkDestroyTestimonial')->name('user.home_page.bulk_delete_testimonial');
        });

        // partners section
        Route::prefix('/partners-section')->controller(PartnerController::class)->group(function () {
            Route::get('', 'index')->name('user.home_page.partners_section');
            Route::post('/store-partner', 'store')->name('user.home_page.store_partner');
            Route::post('/update-partner', 'update')->name('user.home_page.update_partner');
            Route::post('/delete-partner/{id}', 'destroy')->name('user.home_page.delete_partner');
        });

        // section customization
        Route::get('/section-customization', [SectionController::class, 'index'])->name('user.home_page.section_customization');

        Route::post('/update-section-status', [SectionController::class, 'update'])->name('user.home_page.update_section_status');
    });
    // home-page route end



    // announcement-popup route start
    Route::prefix('/announcement-popups')->controller(PopupController::class)->group(function () {
        Route::get('', 'index')->name('user.announcement_popups');
        Route::get('/select-popup-type', 'popupType')->name('user.announcement_popups.select_popup_type');
        Route::get('/create-popup/{type}', 'create')->name('user.announcement_popups.create_popup');
        Route::post('/store-popup', 'store')->name('user.announcement_popups.store_popup');
        Route::post('/popup/{id}/update-status', 'updateStatus')->name('user.announcement_popups.update_popup_status');
        Route::get('/edit-popup/{id}', 'edit')->name('user.announcement_popups.edit_popup');
        Route::post('/update-popup/{id}', 'update')->name('user.announcement_popups.update_popup');
        Route::post('/delete-popup/{id}', 'destroy')->name('user.announcement_popups.delete_popup');
        Route::post('/bulk-delete-popup', 'bulkDestroy')->name('user.announcement_popups.bulk_delete_popup');
    });
    // announcement-popup route end

    // user management route start
    Route::middleware(['checkPackage', 'checkUserPermission:User'])->controller(RegisteredUserController::class)->group(function () {
        Route::get('/registered-users', 'index')->name('user.registered_users');
        Route::post('/user/{id}/update-account-status', 'updateAccountStatus')->name('user.user.update_account_status');
        Route::post('register/users/email', 'emailStatus')->name('user.email');
        Route::get('/user/{id}/details', 'show')->name('user.user_details');
        Route::get('/user/{id}/change-password', 'changePassword')->name('user.user.change_password');
        Route::post('/user/{id}/update-password', 'updatePassword')->name('user.user.update_password');
        Route::post('/user/{id}/delete', 'destroy')->name('user.user.delete');
        Route::post('/user/secret-login', 'secretLogin')->name('user.secretLogin');
        Route::post('/bulk-delete-user', 'bulkDestroy')->name('user.bulk_delete_user');
    });

    // basic settings information route
    Route::get('/contact-form', [BasicController::class, 'contactForm'])->name('user.contact_form');
    Route::post('/contact-form', [BasicController::class, 'updateContactInfo'])->name('user.update.contact_form');

    Route::get('/information', [BasicController::class, 'information'])->name('user.basic_settings.information');
    Route::post('/update-info', [BasicController::class, 'updateInfo'])->name('user.basic_settings.update_info');

    // basic settings page-headings route
    Route::get('/page-headings', [PageHeadingController::class, 'pageHeadings'])->name('user.page_headings');
    Route::post('/update-page-headings', [PageHeadingController::class, 'updatePageHeadings'])->name('user.update_page_headings');

    // user theme change
    Route::get('/change-theme', 'User\UserController@changeTheme')->name('user.theme.change');
    // RTL check
    Route::get('/rtlcheck/{langid}', 'User\LanguageController@rtlcheck')->name('user.rtlcheck');
    Route::get('/change-dashboard-language', [LanguageController::class, 'changeDashboardLanguage'])->name('user.change.dashboard_language');
    Route::controller(UserController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('user-dashboard');
        Route::get('/reset', 'resetform')->name('user-reset');
        Route::post('/reset', 'reset')->name('user-reset-submit');
        Route::get('/profile', 'profile')->name('user-profile');
        Route::post('/profile', 'profileupdate')->name('user-profile-update');
    });
    Route::get('/logout', 'User\Auth\LoginController@logout')->name('user-logout');
    Route::post('/change-status', 'User\UserController@status')->name('user-status');



    // Payment Log
    Route::get('/payment-log', [PaymentLogController::class, 'index'])->name('user.payment-log.index');

    // User Domains & URLs
    Route::controller(DomainController::class)->middleware('checkUserPermission:Custom Domain')->group(function () {
        Route::get('/domains', 'domains')->name('user-domains');
        Route::post('/request/domain', 'domainrequest')->name('user-domain-request');
    });
    // User Domains & URLs
    Route::controller(SubdomainController::class)->middleware('checkUserPermission:Subdomain')->group(function () {
        Route::get('/subdomain', 'subdomain')->name('user-subdomain');
        Route::post('/request/subdomain', 'subdomainrequest')->name('user-subdomain-request');
    });

    // User Subdomains & URLs


    //user follow and following list
    Route::controller(FollowerController::class)->group(function () {
        Route::get('/follower-list', 'follower')->name('user.follower.list');
        Route::get('/following-list', 'following')->name('user.following.list');
        Route::get('/follow/{id}', 'follow')->name('user.follow');
        Route::get('/unfollow/{id}', 'unfollow')->name('user.unfollow');
    });

    Route::get('/change-password', 'User\UserController@changePass')->name('user.changePass');
    Route::post('/profile/updatePassword', 'User\UserController@updatePassword')->name('user.updatePassword');

    //user language
    // checkUserPermission:Additional Language
    Route::controller(LanguageController::class)->group(function () {
        Route::get('/languages', 'index')->name('user.language.index');
        Route::get('/language/{id}/edit', 'edit')->name('user.language.edit')->middleware('packageLimitsCheck:language,update');
        Route::get('/language/{id}/edit/keyword', 'editKeyword')->name('user.language.editKeyword')->middleware('packageLimitsCheck:language,update');
        Route::post('/language/{id}/add/keyword', 'addKeyword')->name('user.language.addKeyword');

        Route::post('/language/{id}/update/keyword', 'updateKeyword')->name('user.language.updateKeyword')->middleware('packageLimitsCheck:language,update');
        Route::post('/language/store', 'store')->name('user.language.store')->middleware('packageLimitsCheck:language,store');

        Route::post('/language/{id}/default', 'default')->name('user.language.default');
        Route::post('/language/{id}/delete', 'delete')->name('user.language.delete');
        Route::post('/language/update', 'update')->name('user.language.update');
    });

    //user theme routes
    Route::controller(BasicController::class)->group(function () {


        // user breadcrumb route
        Route::get('/breadcrumb', 'breadcrumb')->name('user.breadcrumb');
        Route::post('/update_breadcrumb', 'updateBreadcrumb')->name('user.update_breadcrumb');

        Route::get('/mail/information', 'getMailInformation')->name('user.mail.info');
        Route::post('/mail/information', 'storeMailInformation')->name('user.mail.info.update');

        // for user smtp information
        Route::get('/smtp/information', 'getSmtpInformation')->name('user.smtp.info');

        // basic settings appearance route
        Route::get('/appearance', 'appearance')->name('user.appearance');
        Route::post('/update-appearance', 'updateAppearance')->name('user.update.appearance');

        // basic settings seo route
        Route::get('/basic_settings/seo', 'seo')->name('user.basic_settings.seo');
        Route::post('/basic_settings/update_seo_informations', 'updateSEO')->name('user.basic_settings.update_seo_informations');

        // basic settings plugins route start
        Route::get('/plugins', 'plugins')->name('user.plugins');
        Route::post('/update-recapcha', 'updateRecapcha')->name('user.update_recapcha');
        Route::post('/update-disqus', 'updateDisqus')->name('user.update_disqus');
        Route::post('/update-whatsapp', 'updateWhatsApp')->name('user.update_whatsapp');
        Route::post('/update-googlelogin', 'updateGoogle')->name('user.update_google');
        Route::post('/update-googleapi', 'googleapi')->name('user.update_googleapi');
        Route::post('/update-aiupdate', 'aiupdate')->name('user.aiupdate');
        Route::post('/update-aws-credentials', 'updateAWSCredentials')->name('user.update_aws_credentials');
        // basic settings plugins route end

        // basic settings maintenance-mode route
        Route::get('/maintenance-mode', 'maintenance')->name('user.maintenance_mode');
        Route::post('/update-maintenance-mode', 'updateMaintenance')->name('user.update_maintenance_mode');
    });

    Route::controller(MailTemplateController::class)->group(function () {
        Route::get('/mail-templates', 'index')->name('user.mail_templates');
        Route::get('/edit-mail-template/{id}', 'edit')->name('user.edit_mail_template');
        Route::post('/update-mail-template/{id}', 'update')->name('user.update_mail_template');
    });

    // basic settings cookie-alert route
    Route::controller(CookieAlertController::class)->group(function () {
        Route::get('/cookie-alert', 'cookieAlert')->name('user.cookie_alert');
        Route::post('/update-cookie-alert', 'updateCookieAlert')->name('user.update_cookie_alert');
    });

    // start user menu builder
    Route::controller(MenuBuilderController::class)->group(function () {
        Route::get('/menu-builder', 'index')->name('user.menu_builder.index');
        Route::post('/menu-builder/update', 'update')->name('user.menu_builder.update');
    });
    // end user menu builder

    // user Social routes
    Route::controller(SocialController::class)->group(function () {
        Route::get('/social', 'index')->name('user.social.index');
        Route::post('/social/store', 'store')->name('user.social.store');
        Route::get('/social/{id}/edit', 'edit')->name('user.social.edit');
        Route::post('/social/update', 'update')->name('user.social.update');
        Route::post('/social/delete', 'delete')->name('user.social.delete');
    });
    // faq route start
    Route::prefix('/faq-management')->controller(FaqController::class)->group(function () {
        Route::get('', 'index')->name('user.faq_management');
        Route::post('/store-faq', 'store')->name('user.faq_management.store_faq');
        Route::post('/update-faq', 'update')->name('user.faq_management.update_faq');
        Route::post('/delete-faq/{id}', 'destroy')->name('user.faq_management.delete_faq');
        Route::post('/bulk-delete-faq', 'bulkDestroy')->name('user.faq_management.bulk_delete_faq');
    });
    // faq route end

    // blog route start
    Route::prefix('/blog-management')->middleware('checkUserPermission:Blog')->group(function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index')->name('user.blog_management.categories');
            Route::post('/store-category', 'store')->name('user.blog_management.store_category');
            Route::put('/update-category', 'update')->name('user.blog_management.update_category');
            Route::post('/delete-category/{id}', 'destroy')->name('user.blog_management.delete_category');
            Route::post('/bulk-delete-category', 'bulkDestroy')->name('user.blog_management.bulk_delete_category');
        });

        Route::controller(BlogController::class)->group(function () {
            Route::get('/blogs', 'index')->name('user.blog_management.blogs');
            Route::get('/create-blog', 'create')->name('user.blog_management.create_blog');
            Route::post('/store-blog', 'store')->name('user.blog_management.store_blog')->middleware('packageLimitsCheck:blog,store');
            Route::get('/edit-blog/{id}', 'edit')->name('user.blog_management.edit_blog');
            Route::post('/update-blog/{id}', 'update')->name('user.blog_management.update_blog')->middleware('packageLimitsCheck:blog,update');
            Route::post('/delete-blog/{id}', 'destroy')->name('user.blog_management.delete_blog');
            Route::post('/bulk-delete-blog', 'bulkDestroy')->name('user.blog_management.bulk_delete_blog');
        });
    });
    // blog route end

    // custom-pages route start
    Route::prefix('/custom-pages')->controller(CustomPageController::class)->middleware('checkUserPermission:Additional Page')->group(function () {
        Route::get('', 'index')->name('user.custom_pages');
        Route::get('/create-page', 'create')->name('user.custom_pages.create_page');
        Route::post('/store-page', 'store')->name('user.custom_pages.store_page')->middleware('packageLimitsCheck:customPage,store');
        Route::get('/edit-page/{id}', 'edit')->name('user.custom_pages.edit_page');
        Route::post('/update-page/{id}', 'update')->name('user.custom_pages.update_page')->middleware('packageLimitsCheck:customPage,update');
        Route::post('/delete-page/{id}', 'destroy')->name('user.custom_pages.delete_page');
        Route::post('/bulk-delete-page', 'bulkDestroy')->name('user.custom_pages.bulk_delete_page');
    });
    // custom-pages route end

    // advertise route start
    Route::prefix('/advertisements')->middleware('checkUserPermission:Advertisement')->group(function () {
        Route::get('/settings', 'User\BasicController@advertiseSettings')->name('user.advertise.settings');
        Route::post('/update-settings', 'User\BasicController@updateAdvertiseSettings')->name('user.advertise.update_settings');

        Route::controller(AdvertisementController::class)->group(function () {
            Route::get('/', 'index')->name('user.advertisements');
            Route::post('/store-advertisement', 'store')->name('user.advertise.store_advertisement');
            Route::post('/update-advertisement', 'update')->name('user.advertise.update_advertisement');
            Route::post('/delete-advertisement/{id}', 'destroy')->name('user.advertise.delete_advertisement');
            Route::post('/bulk-delete-advertisement', 'bulkDestroy')->name('user.advertise.bulk_delete_advertisement');
        });
    });
    // advertise route end

    // Summernote image upload
    Route::post('/summernote/upload', 'Admin\SummernoteController@upload')->name('user.summernote.upload');

    //user package extend route
    Route::controller(BuyPlanController::class)->group(function () {
        Route::get('/package-list', 'index')->name('user.plan.extend.index');
        Route::get('/package/checkout/{package_id}', 'checkout')->name('user.plan.extend.checkout');
    });
    Route::post('/package/checkout', 'User\UserCheckoutController@checkout')->name('user.plan.checkout');

    //user footer route
    Route::controller(FooterController::class)->group(function () {
        Route::get('/footer/logo&image', 'logo')->name('user.footer.logo');
        Route::post('/footer/update-logo', 'updateLogo')->name('user.footer.update_logo');
        Route::post('/footer/update-bg-image', 'updateBgImage')->name('user.footer.update_bg_image');
        Route::get('/footer/content', 'footerContent')->name('user.footer.content');
        Route::post('/footer/update-content/{language}', 'updateFooterContent')->name('user.footer.update_content');
        Route::get('/footer/quick_links', 'quickLinks')->name('user.footer.quick_links');
        Route::post('/footer/store_quick_link', 'storeQuickLink')->name('user.footer.store_quick_link');
        Route::post('/footer/update_quick_link', 'updateQuickLink')->name('user.footer.update_quick_link');
        Route::post('/footer/delete_quick_link', 'deleteQuickLink')->name('user.footer.delete_quick_link');
    });
    //user subscriber routes
    Route::controller(SubscriberController::class)->group(function () {
        Route::get('/subscribers', 'index')->name('user.subscriber.index');
        Route::get('/mailsubscriber', 'mailsubscriber')->name('user.mailsubscriber');
        Route::post('/subscribers/sendmail', 'subscsendmail')->name('user.subscribers.sendmail');
        Route::post('/subscriber/delete', 'delete')->name('user.subscriber.delete');
        Route::post('/subscriber/bulk-delete', 'bulkDelete')->name('user.subscriber.bulk.delete');
    });
});


Route::group(['middleware' => ['web', 'setlang']], function () {
    Route::controller(CheckoutController::class)->group(function () {
        Route::post('/coupon',  'coupon')->name('front.membership.coupon');
        Route::post('/membership/checkout',  'checkout')->name('front.membership.checkout');
        Route::post('/payment/instructions',  'paymentInstruction')->name('front.payment.instructions');
        Route::post('/contact/message',  'contactMessage')->name('front.contact.message');
        // Route::post('/admin/contact-msg',  'adminContactMessage')->name('front.admin.contact.message');
    });

    //checkout payment gateway routes
    Route::prefix('membership')->middleware('setlang')->group(function () {
        Route::get('paypal/success', "Payment\PaypalController@successPayment")->name('membership.paypal.success');
        Route::get('paypal/cancel', "Payment\PaypalController@cancelPayment")->name('membership.paypal.cancel');
        Route::get('stripe/cancel', "Payment\StripeController@cancelPayment")->name('membership.stripe.cancel');
        Route::post('paytm/payment-status', "Payment\PaytmController@paymentStatus")->name('membership.paytm.status');
        Route::get('paystack/success', 'Payment\PaystackController@successPayment')->name('membership.paystack.success');
        Route::get('paystack/cancle', 'Payment\PaystackController@cancelPayment')->name('membership.paystack.cancle');
        Route::post('mercadopago/cancel', 'Payment\MercadopagoController@cancelPayment')->name('membership.mercadopago.cancel');
        Route::get('mercadopago/success', 'Payment\MercadopagoController@successPayment')->name('membership.mercadopago.success');
        Route::post('razorpay/success', 'Payment\RazorpayController@successPayment')->name('membership.razorpay.success');
        Route::post('razorpay/cancel', 'Payment\RazorpayController@cancelPayment')->name('membership.razorpay.cancel');
        Route::get('instamojo/success', 'Payment\InstamojoController@successPayment')->name('membership.instamojo.success');
        Route::post('instamojo/cancel', 'Payment\InstamojoController@cancelPayment')->name('membership.instamojo.cancel');
        Route::get('flutterwave/success', 'Payment\FlutterWaveController@successPayment')->name('membership.flutterwave.success');
        Route::get('flutterwave/cancel', 'Payment\FlutterWaveController@cancelPayment')->name('membership.flutterwave.cancel');
        Route::get('/mollie/success', 'Payment\MollieController@successPayment')->name('membership.mollie.success');
        Route::post('mollie/cancel', 'Payment\MollieController@cancelPayment')->name('membership.mollie.cancel');
        Route::get('anet/cancel', 'Payment\AuthorizenetController@cancelPayment')->name('membership.anet.cancel');
        Route::get('/phonepe/success', 'Payment\PhonePeController@successPayment')->name('membership.phonepe.success');
        Route::get('phonepe/cancel', 'Payment\PhonePeController@cancelPayment')->name('membership.phonepe.cancel');
        Route::get('/perfect_money/success', 'Payment\PerfectMoneyController@successPayment')->name('membership.perfect_money.success');
        Route::get('perfect_money/cancel', 'Payment\PerfectMoneyController@cancelPayment')->name('membership.perfect_money.cancel');
        Route::get('/xendit/success', 'Payment\XenditController@successPayment')->name('membership.xendit.success');
        Route::get('/yoco/success', 'Payment\YocoController@successPayment')->name('membership.yoco.success');
        Route::get('/xendit/success', 'Payment\XenditController@successPayment')->name('membership.xendit.success');

        Route::get('/toyyibpay/success', 'Payment\ToyyibpayController@successPayment')->name('membership.toyyibpay.success');
        Route::post('/paytabs/success', 'Payment\PaytabsController@successPayment')->name('membership.paytabs.success');
        Route::post('/iyzico/success', 'Payment\IyzicoController@successPayment')->name('membership.iyzico.success');

        Route::get('/midtrans/notify/{orderId?}', [MidtransController::class, 'successPayment'])->name('membership.midtrans.success');
        Route::get('/midtrans/bank-notify', [MidtransController::class, 'bankNotify'])->name('membership.midtrans.bank.success');

        Route::get('midtrans/cancel', [CheckoutController::class, 'paymentCancel'])->name('membership.midtrans.payment.cancel');

        Route::get('/cancel', [CheckoutController::class, 'paymentCancel'])->name('membership.payment.cancel');
        // Route::get('/toyyibpay/success', [ToyyibpayController::class, 'trialSuccess'])->name('membership.toyyibpay.success');

        Route::get('/offline/success', [CheckoutController::class, 'offlineSuccess'])->name('membership.offline.success');
        Route::get('/trial/success', [CheckoutController::class, 'trialSuccess'])->name('membership.trial.success');
    });
});
