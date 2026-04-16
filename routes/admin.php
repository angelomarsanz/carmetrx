<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PopupController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\UlinkController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\GatewayController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ProcessController;
use App\Http\Controllers\Admin\SitemapController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\BcategoryController;
use App\Http\Controllers\Admin\SubdomainController;
use App\Http\Controllers\Admin\HerosectionController;
use App\Http\Controllers\Admin\MenuBuilderController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\CustomDomainController;
use App\Http\Controllers\Admin\HomePageTextController;
use App\Http\Controllers\Admin\IntrosectionController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\RegisterUserController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\PropertyCategoryController;
use App\Http\Controllers\Admin\AdditionalSectionController;
use App\Http\Controllers\Admin\AboutAdditionSectionController;

$domain = env('WEBSITE_HOST');

if (!app()->runningInConsole() && isset($_SERVER['HTTP_HOST'])) {
    if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.') {
        $domain = 'www.' . env('WEBSITE_HOST');
    }
}



/*=======================================================
******************** Admin Routes **********************
=======================================================*/

Route::domain($domain)->group(function () use ($domain) {

    Route::prefix('admin')->middleware(['adminLang'])->group(function () {
        Route::group(['middleware' => 'guest:admin'], function () {
            Route::get('/', 'Admin\LoginController@login')->name('admin.login');
            Route::post('/login', 'Admin\LoginController@authenticate')->name('admin.auth');

            Route::get('/mail-form', 'Admin\ForgetController@mailForm')->name('admin.forget.form');
            Route::post('/sendmail', 'Admin\ForgetController@sendmail')->name('admin.forget.mail');
        });


        //property specifications
        Route::prefix('property-specifications')->group(function () {
            // property category route
            Route::controller(PropertyCategoryController::class)->group(function () {
                Route::get('/categories', 'index')->name('admin.property_management.categories');
                Route::post('/store-category', 'store')->name('admin.property_management.store_category');
                Route::post('/update-category', 'update')->name('admin.property_management.update_category');
                Route::post('/update-category-featured', 'updateFeatured')->name('admin.property_management.update_category_featured');
                Route::post('/delete-category', 'destroy')->name('admin.property_management.delete_category');
                Route::post('/bulk-delete-category', 'bulkDestroy')->name('admin.property_management.bulk_delete_category');
            });

            // property Amenities route
            Route::controller(AmenityController::class)->group(function () {
                Route::get('/amenity', 'index')->name('admin.property_management.amenities');
                Route::post('/store-amenity', 'store')->name('admin.property_management.store_amenity');
                Route::post('/update-amenity', 'update')->name('admin.property_management.update_amenity');
                Route::post('/delete-amenity', 'destroy')->name('admin.property_management.delete_amenity');
                Route::post('/bulk-delete-amenity', 'bulkDestroy')->name('admin.property_management.bulk_delete_amenity');
            });

            // property countries route
            Route::controller(CountryController::class)->group(function () {
                Route::get('/states', 'index')->name('admin.property_management.countries');
                Route::post('/store-country', 'store')->name('admin.property_management.store_country');
                Route::post('/update-country', 'update')->name('admin.property_management.update_country');
                Route::post('/delete-country', 'destroy')->name('admin.property_management.delete_country');
                Route::post('/bulk-delete-country', 'bulkDestroy')->name('admin.property_management.bulk_delete_country');
            });

            // property countries route
            Route::controller(StateController::class)->group(function () {
                Route::get('/cities', 'index')->name('admin.property_management.states');
                Route::get('/get-state', 'getState')->name('admin.property_management.get_state');
                Route::get('/get-states-cities', 'getStateCities')->name('admin.property_management.get_state_cities');
                Route::post('/store-state', 'store')->name('admin.property_management.store_state');
                Route::post('/update-state', 'update')->name('admin.property_management.update_state');
                Route::post('/delete-state', 'destroy')->name('admin.property_management.delete_state');
                Route::post('/bulk-delete-state', 'bulkDestroy')->name('admin.property_management.bulk_delete_state');
            });

            // property cities route
            Route::controller(CityController::class)->group(function () {
                Route::get('/areas', 'index')->name('admin.property_management.cities');
                Route::get('/get-cities', 'getCities')->name('admin.property_management.get_cities');
                Route::post('/store-city', 'store')->name('admin.property_management.store_city');
                Route::post('/update-city', 'update')->name('admin.property_management.update_city');
                Route::post('/update-featured', 'updateFeatured')->name('admin.property_management.update_city_featured');
                Route::post('/delete-city', 'destroy')->name('admin.property_management.delete_city');
                Route::post(
                    '/bulk-delete-city',
                    'bulkDestroy'
                )->name('admin.property_management.bulk_delete_city');
            });

            //property types
            Route::controller(PropertyTypeController::class)->group(function () {
                Route::get('/property-types', 'index')->name('admin.property_management.property_types');
                Route::post('/property-types/update', 'update')->name('admin.property_management.property_types_update');
                Route::get('/settings', 'settings')->name('admin.property_management.settings');
                Route::post('/update-settings', 'update_settings')->name('admin.property_management.update_settings');
            });
        });


        Route::group(['middleware' => ['auth:admin', 'checkstatus']], function () {
            // RTL check
            Route::get('/rtlcheck/{langid}', 'Admin\LanguageController@rtlcheck')->name('admin.rtlcheck');
            // admin redirect to dashboard route
            Route::get('/change-theme', 'Admin\DashboardController@changeTheme')->name('admin.theme.change');
            // change admin language
            Route::get('/change-dashboard-language', [LanguageController::class, 'changeDashboardLanguage'])->name('admin.change.dashboard_language');
            // Summernote image upload
            Route::post('/summernote/upload', 'Admin\SummernoteController@upload')->name('admin.summernote.upload');
            // Admin logout Route
            Route::get('/logout', 'Admin\LoginController@logout')->name('admin.logout');
            // Admin Dashboard Routes
            Route::group(['middleware' => 'checkpermission:Dashboard'], function () {
                Route::get('/dashboard', 'Admin\DashboardController@dashboard')->name('admin.dashboard');
            });
            // Admin Profile Routes
            Route::get('/changePassword', 'Admin\ProfileController@changePass')->name('admin.changePass');
            Route::post('/profile/updatePassword', 'Admin\ProfileController@updatePassword')->name('admin.updatePassword');
            Route::get('/profile/edit', 'Admin\ProfileController@editProfile')->name('admin.editProfile');
            Route::post('/profile/update', 'Admin\ProfileController@updateProfile')->name('admin.updateProfile');

            Route::group(['middleware' => 'checkpermission:Settings'], function () {
                // Admin Basic Information Routes
                Route::get('/basicinfo', 'Admin\BasicController@basicinfo')->name('admin.basicinfo');
                Route::post('/basicinfo/post', 'Admin\BasicController@updatebasicinfo')->name('admin.basicinfo.update');
                // Admin Email Settings Routes
                Route::get('/mail-from-admin', 'Admin\EmailController@mailFromAdmin')->name('admin.mailFromAdmin');
                Route::post('/mail-from-admin/update', 'Admin\EmailController@updateMailFromAdmin')->name('admin.mailfromadmin.update');
                Route::get('/mail-to-admin', 'Admin\EmailController@mailToAdmin')->name('admin.mailToAdmin');
                Route::post('/mail-to-admin/update', 'Admin\EmailController@updateMailToAdmin')->name('admin.mailtoadmin.update');

                Route::get('/mail_templates', 'Admin\MailTemplateController@mailTemplates')->name('admin.mail_templates');
                Route::get('/edit_mail_template/{id}', 'Admin\MailTemplateController@editMailTemplate')->name('admin.edit_mail_template');
                Route::post('/update_mail_template/{id}', 'Admin\MailTemplateController@updateMailTemplate')->name('admin.update_mail_template');

                // Admin Scripts Routes
                Route::get('/script', 'Admin\BasicController@script')->name('admin.script');
                Route::post('/script/update', 'Admin\BasicController@updatescript')->name('admin.script.update');
                // Admin Social Routes
                Route::get('/social', 'Admin\SocialController@index')->name('admin.social.index');
                Route::post('/social/store', 'Admin\SocialController@store')->name('admin.social.store');
                Route::get('/social/{id}/edit', 'Admin\SocialController@edit')->name('admin.social.edit');
                Route::post('/social/update', 'Admin\SocialController@update')->name('admin.social.update');
                Route::post('/social/delete', 'Admin\SocialController@delete')->name('admin.social.delete');
                // Admin Maintenance Mode Routes
                Route::get('/maintainance', 'Admin\BasicController@maintainance')->name('admin.maintainance');
                Route::post('/maintainance/update', 'Admin\BasicController@updatemaintainance')->name('admin.maintainance.update');
                // Admin Section Customization Routes
                Route::get('/sections', 'Admin\BasicController@sections')->name('admin.sections.index');
                Route::post('/sections/update', 'Admin\BasicController@updatesections')->name('admin.sections.update');
                // Admin Cookie Alert Routes
                Route::get('/cookie-alert', 'Admin\BasicController@cookiealert')->name('admin.cookie.alert');
                Route::post('/cookie-alert/{langid}/update', 'Admin\BasicController@updatecookie')->name('admin.cookie.update');
                // basic settings seo route
                Route::get('/seo', 'Admin\BasicController@seo')->name('admin.seo');
                Route::post('/seo/update', 'Admin\BasicController@updateSEO')->name('admin.seo.update');
            });

            Route::group(['middleware' => 'checkpermission:Subscribers'], function () {
                // Admin Subscriber Routes
                Route::get('/subscribers', 'Admin\SubscriberController@index')->name('admin.subscriber.index');
                Route::get('/mailsubscriber', 'Admin\SubscriberController@mailsubscriber')->name('admin.mailsubscriber');
                Route::post('/subscribers/sendmail', 'Admin\SubscriberController@subscsendmail')->name('admin.subscribers.sendmail');
                Route::post('/subscriber/delete', 'Admin\SubscriberController@delete')->name('admin.subscriber.delete');
                Route::post('/subscriber/bulk-delete', 'Admin\SubscriberController@bulkDelete')->name('admin.subscriber.bulk.delete');
            });

            Route::controller(MenuBuilderController::class)->middleware('checkpermission:Menu Builder')->group(function () {
                Route::get('/menu-builder', 'index')->name('admin.menu_builder.index');
                Route::post('/menu-builder/update', 'update')->name('admin.menu_builder.update');
            });

            Route::group(['middleware' => 'checkpermission:Home Page'], function () {

                Route::get('/user-themes', 'Admin\BasicController@userThemes')->name('admin.userThemes');
                Route::post('/user-themes/store', 'Admin\BasicController@userThemeStore')->name('admin.userThemes.store');
                Route::post('/user-themes/update', 'Admin\BasicController@userThemeUpdate')->name('admin.userThemes.update');
                Route::post('/user-themes/change-status', 'Admin\BasicController@changeThemeStatus')->name('admin.userThemes.statusChange');
                Route::post('/user-themes/delete', 'Admin\BasicController@themeDelete')->name('admin.userThemes.delete');

                // Admin Hero Section Image & Text Routes
                Route::controller(HerosectionController::class)->group(function () {
                    Route::get('/herosection/imgtext', 'imgtext')->name('admin.herosection.imgtext');
                    Route::post('/herosection/{langid}/update', 'update')->name('admin.herosection.update');
                });

                // Admin Feature Routes
                Route::controller(FeatureController::class)->group(function () {
                    Route::get('/features', 'index')->name('admin.feature.index');
                    Route::post('/feature/store', 'store')->name('admin.feature.store');
                    Route::get('/feature/{id}/edit', 'edit')->name('admin.feature.edit');
                    Route::post('/feature/update', 'update')->name('admin.feature.update');
                    Route::post('/feature/delete', 'delete')->name('admin.feature.delete');
                });
                // Admin Work Process Routes
                Route::controller(ProcessController::class)->group(function () {
                    Route::get('/process', 'index')->name('admin.process.index');
                    Route::post('/process/store', 'store')->name('admin.process.store');
                    Route::get('/process/{id}/edit', 'edit')->name('admin.process.edit');
                    Route::post('/process/update', 'update')->name('admin.process.update');
                    Route::post('/process/delete', 'delete')->name('admin.process.delete');
                });
                // Admin Intro Section Routes
                Route::controller(IntrosectionController::class)->group(function () {
                    Route::get('/introsection', 'index')->name('admin.introsection.index');
                    Route::post('/introsection/{langid}/update', 'update')->name('admin.introsection.update');
                    Route::post('/introsection/remove/image', 'removeImage')->name('admin.introsection.img.rmv');
                });
                // Admin Testimonial Routes
                Route::controller(TestimonialController::class)->group(function () {
                    Route::get('/testimonials', 'index')->name('admin.testimonial.index');
                    Route::get('/testimonial/create', 'create')->name('admin.testimonial.create');
                    Route::post('/testimonial/store', 'store')->name('admin.testimonial.store');
                    Route::post('/testimonial/sideImageStore', 'sideImageStore')->name('admin.testimonial.sideImageStore');
                    Route::get('/testimonial/{id}/edit', 'edit')->name('admin.testimonial.edit');
                    Route::post('/testimonial/update', 'update')->name('admin.testimonial.update');
                    Route::post('/testimonial/delete', 'delete')->name('admin.testimonial.delete');
                    Route::post('/testimonialtext/{langid}/update', 'textupdate')->name('admin.testimonialtext.update');
                });
                // Admin home page text routes

                Route::controller(HomePageTextController::class)->group(function () {

                    Route::get('/home-page-text-section', 'index')->name('admin.home.page.text.index');
                    Route::post('/home-page-text-section/{langid}/update', 'update')->name('admin.home.page.text.update');
                });

                // Admin Partner Routes
                Route::controller(PartnerController::class)->group(function () {
                    Route::get('/partners', 'index')->name('admin.partner.index');
                    Route::post('/partner/store', 'store')->name('admin.partner.store');
                    Route::post('/partner/upload', 'upload')->name('admin.partner.upload');
                    Route::get('/partner/{id}/edit', 'edit')->name('admin.partner.edit');
                    Route::post('/partner/update', 'update')->name('admin.partner.update');
                    Route::post('/partner/{id}/uploadUpdate', 'uploadUpdate')->name('admin.partner.uploadUpdate');
                    Route::post('/partner/delete', 'destroy')->name('admin.partner.delete');
                });
            });

            //about us pages
            Route::prefix('about-us')->group(function () {
                Route::get('/update-section-status', 'Admin\BasicController@aboutSectionInfo')->name('admin.about_us.section.hide_show');
                Route::post('/update-section-status/update', 'Admin\BasicController@aboutSectionInfoUpdate')->name('admin.about_us.section.hide_show.update');
            });

            //additional sections
            Route::prefix('additional-sections-about-us')->controller(AboutAdditionSectionController::class)->group(function () {
                Route::get('sections', 'index')->name('admin.about_us.additional_sections');
                Route::get('add-section', 'create')->name('admin.about_us.additional_section.create');
                Route::post('store-section', 'store')->name('admin.about_us.additional_section.store');
                Route::get('edit-section/{id}', 'edit')->name('admin.about_us.additional_section.edit');
                Route::post('update/{id}', 'update')->name('admin.about_us.additional_section.update');
                Route::post('delete/{id}', 'delete')->name('admin.about_us.additional_section.delete');
                Route::post('bulkdelete', 'bulkdelete')->name('admin.about_us.additional_section.bulkdelete');
            });

            Route::controller(PageController::class)->middleware('checkpermission:Pages')->group(function () {
                // Menu Manager Routes
                Route::get('/pages', 'index')->name('admin.page.index');
                Route::get('/page/create', 'create')->name('admin.page.create');
                Route::post('/page/store', 'store')->name('admin.page.store');
                Route::get('/page/{menuID}/edit', 'edit')->name('admin.page.edit');
                Route::post('/page/update', 'update')->name('admin.page.update');
                Route::post('/page/delete', 'delete')->name('admin.page.delete');
                Route::post('/page/bulk-delete', 'bulkDelete')->name('admin.page.bulk.delete');
            });

            //aditional section routes

            Route::prefix('additional-sections')->controller(AdditionalSectionController::class)->group(function () {
                Route::get('sections', 'index')->name('admin.additional_sections');
                Route::get('add-section', 'create')->name('admin.additional_section.create');
                Route::post('store-section', 'store')->name('admin.additional_section.store');
                Route::get('edit-section/{id}', 'edit')->name('admin.additional_section.edit');
                Route::post('update/{id}', 'update')->name('admin.additional_section.update');
                Route::post('delete/{id}', 'delete')->name('admin.additional_section.delete');
                Route::post('bulkdelete', 'Admin\AdditionalSectionController@bulkdelete')->name('admin.additional_section.bulkdelete');
            });


            Route::group(['middleware' => 'checkpermission:Footer'], function () {
                // Admin Footer Logo Text Routes
                Route::controller(FooterController::class)->group(function () {
                    Route::get('/footers', 'index')->name('admin.footer.index');
                    Route::post('/footer/{langid}/update', 'update')->name('admin.footer.update');
                    Route::post('/footer/remove/image', 'removeImage')->name('admin.footer.rmvimg');
                });

                // Admin Useful link Routes
                Route::controller(UlinkController::class)->group(function () {
                    Route::get('/ulinks', 'index')->name('admin.ulink.index');
                    Route::get('/ulink/create', 'create')->name('admin.ulink.create');
                    Route::post('/ulink/store', 'store')->name('admin.ulink.store');
                    Route::get('/ulink/{id}/edit', 'edit')->name('admin.ulink.edit');
                    Route::post('/ulink/update', 'update')->name('admin.ulink.update');
                    Route::post('/ulink/delete', 'delete')->name('admin.ulink.delete');
                });
            });

            // Announcement Popup Routes
            Route::controller(PopupController::class)->middleware('checkpermission:Announcement Popup')->group(function () {
                Route::get('popups', 'index')->name('admin.popup.index');
                Route::get('popup/types', 'types')->name('admin.popup.types');
                Route::get('popup/{id}/edit', 'edit')->name('admin.popup.edit');
                Route::get('popup/create', 'create')->name('admin.popup.create');
                Route::post('popup/store', 'store')->name('admin.popup.store');
                Route::post('popup/delete', 'delete')->name('admin.popup.delete');
                Route::post('popup/bulk-delete', 'bulkDelete')->name('admin.popup.bulk.delete');
                Route::post('popup/status', 'status')->name('admin.popup.status');
                Route::post('popup/update', 'update')->name('admin.popup.update');
            });

            Route::controller(RegisterUserController::class)->middleware('checkpermission:Registered Users')->group(function () {
                // Register User start
                Route::get('register/users', 'index')->name('admin.register.user');
                Route::post('register/user/store', 'store')->name('register.user.store');
                Route::post('register/users/ban', 'userban')->name('register.user.ban');
                Route::post('register/users/featured', 'userFeatured')->name('register.user.featured');
                Route::post('register/users/template', 'userTemplate')->name('register.user.template');
                Route::post('register/users/template/update', 'userUpdateTemplate')->name('register.user.updateTemplate');
                Route::post('register/users/email', 'emailStatus')->name('register.user.email');
                Route::get('register/user/details/{id}', 'view')->name('register.user.view');
                Route::post('/user/current-package/remove', 'removeCurrPackage')->name('user.currPackage.remove');
                Route::post('/user/current-package/change', 'changeCurrPackage')->name('user.currPackage.change');
                Route::post('/user/current-package/add', 'addCurrPackage')->name('user.currPackage.add');
                Route::post('/user/next-package/remove', 'removeNextPackage')->name('user.nextPackage.remove');
                Route::post('/user/next-package/change', 'changeNextPackage')->name('user.nextPackage.change');
                Route::post('/user/next-package/add', 'addNextPackage')->name('user.nextPackage.add');
                Route::post('register/user/delete', 'delete')->name('register.user.delete');
                Route::post('register/user/bulk-delete', 'bulkDelete')->name('register.user.bulk.delete');
                Route::get('register/user/{id}/changePassword', 'changePass')->name('register.user.changePass');
                Route::post('register/user/updatePassword', 'updatePassword')->name('register.user.updatePassword');
                Route::post('register/user/secret-login', 'secretLogin')->name('register.user.secretLogin');
                //Register User end
            });

            // Admin FAQ Routes
            Route::controller(FaqController::class)->middleware('checkpermission:FAQ Management')->group(function () {
                Route::get('/faqs', 'index')->name('admin.faq.index');
                Route::get('/faq/create', 'create')->name('admin.faq.create');
                Route::post('/faq/store', 'store')->name('admin.faq.store');
                Route::post('/faq/update', 'update')->name('admin.faq.update');
                Route::post('/faq/delete', 'delete')->name('admin.faq.delete');
                Route::post('/faq/bulk-delete', 'bulkDelete')->name('admin.faq.bulk.delete');
            });


            // Admin Blog Category Routes
            Route::group(['middleware' => 'checkpermission:Blogs'], function () {
                Route::controller(BcategoryController::class)->group(function () {
                    Route::get('/bcategories', 'index')->name('admin.bcategory.index');
                    Route::post('/bcategory/store', 'store')->name('admin.bcategory.store');
                    Route::post('/bcategory/update', 'update')->name('admin.bcategory.update');
                    Route::post('/bcategory/delete', 'delete')->name('admin.bcategory.delete');
                    Route::post('/bcategory/bulk-delete', 'bulkDelete')->name('admin.bcategory.bulk.delete');
                });

                // Admin Blog Routes
                Route::controller(BlogController::class)->group(function () {
                    Route::get('/blogs', 'index')->name('admin.blog.index');
                    Route::post('/blog/upload', 'upload')->name('admin.blog.upload');
                    Route::post('/blog/store', 'store')->name('admin.blog.store');
                    Route::get('/blog/{id}/edit', 'edit')->name('admin.blog.edit');
                    Route::post('/blog/update', 'update')->name('admin.blog.update');
                    Route::post('/blog/{id}/uploadUpdate', 'uploadUpdate')->name('admin.blog.uploadUpdate');
                    Route::post('/blog/delete', 'delete')->name('admin.blog.delete');
                    Route::post('/blog/bulk-delete', 'bulkDelete')->name('admin.blog.bulk.delete');
                    Route::get('/blog/{langid}/getcats', 'getcats')->name('admin.blog.getcats');
                });
            });

            Route::controller(SitemapController::class)->middleware('checkpermission:Sitemap')->group(function () {
                Route::get('/sitemap', 'index')->name('admin.sitemap.index');
                Route::post('/sitemap/store', 'store')->name('admin.sitemap.store');
                Route::get('/sitemap/{id}/update', 'update')->name('admin.sitemap.update');
                Route::post('/sitemap/{id}/delete', 'delete')->name('admin.sitemap.delete');
                Route::post('/sitemap/download', 'download')->name('admin.sitemap.download');
            });

            Route::controller(ContactController::class)->middleware('checkpermission:Contact Page')->group(function () {
                // Admin Contact Routes
                Route::get('/contact', 'index')->name('admin.contact.index');
                Route::post('/contact/{langid}/post', 'update')->name('admin.contact.update');
            });

            Route::controller(GatewayController::class)->middleware('checkpermission:Payment Gateways')->group(function () {
                // Admin Online Gateways Routes
                Route::get('/gateways', 'index')->name('admin.gateway.index');
                Route::post('/stripe/update', 'stripeUpdate')->name('admin.stripe.update');
                Route::post('/anet/update', 'anetUpdate')->name('admin.anet.update');
                Route::post('/paypal/update', 'paypalUpdate')->name('admin.paypal.update');
                Route::post('/paystack/update', 'paystackUpdate')->name('admin.paystack.update');
                Route::post('/paytm/update', 'paytmUpdate')->name('admin.paytm.update');
                Route::post('/flutterwave/update', 'flutterwaveUpdate')->name('admin.flutterwave.update');
                Route::post('/instamojo/update', 'instamojoUpdate')->name('admin.instamojo.update');
                Route::post('/mollie/update', 'mollieUpdate')->name('admin.mollie.update');
                Route::post('/razorpay/update', 'razorpayUpdate')->name('admin.razorpay.update');
                Route::post('/mercadopago/update', 'mercadopagoUpdate')->name('admin.mercadopago.update');

                Route::post('/yoco-update', 'updateYocoInfo')->name('admin.yoco.update');
                Route::post('/zendit-update', 'updateXenditInfo')->name('admin.xendit.update');
                Route::post('/perfect-update', 'updatePerfectMoneyInfo')->name('admin.perfect.update');
                Route::post('/myfatoorah-update', 'updateMyFatoorahInfo')->name('admin.myfatoorah.update');
                Route::post('/iyzico-update', 'updateIyzicoInfo')->name('admin.iyzico.update');
                Route::post('/paytabs-update', 'updatePaytabsInfo')->name('admin.paytabs.update');
                Route::post('/toyyibpay-update', 'updateToyyibpayInfo')->name('admin.toyyibpay.update');
                Route::post('/midtrans-update', 'updateMidtransInfo')->name('admin.midtrans.update');
                Route::post('/phonepe-update', 'updatePhonepeInfo')->name('admin.phonepe.update');


                // Admin Offline Gateway Routes
                Route::get('/offline/gateways', 'offline')->name('admin.gateway.offline');
                Route::post('/offline/gateway/store', 'store')->name('admin.gateway.offline.store');
                Route::post('/offline/gateway/update', 'update')->name('admin.gateway.offline.update');
                Route::post('/offline/status', 'status')->name('admin.offline.status');
                Route::post('/offline/gateway/delete', 'delete')->name('admin.offline.gateway.delete');
            });

            Route::group(['middleware' => 'checkpermission:Role Management'], function () {
                // Admin Roles Routes
                Route::get('/roles', 'Admin\RoleController@index')->name('admin.role.index');
                Route::post('/role/store', 'Admin\RoleController@store')->name('admin.role.store');
                Route::post('/role/update', 'Admin\RoleController@update')->name('admin.role.update');
                Route::post('/role/delete', 'Admin\RoleController@delete')->name('admin.role.delete');
                Route::get('role/{id}/permissions/manage', 'Admin\RoleController@managePermissions')->name('admin.role.permissions.manage');
                Route::post('role/permissions/update', 'Admin\RoleController@updatePermissions')->name('admin.role.permissions.update');
            });

            Route::controller(UserController::class)->middleware('checkpermission:Admins Management')->group(function () {
                // Admin Users Routes
                Route::get('/users', 'index')->name('admin.user.index');
                Route::post('/user/upload', 'upload')->name('admin.user.upload');
                Route::post('/user/store', 'store')->name('admin.user.store');
                Route::get('/user/{id}/edit', 'edit')->name('admin.user.edit');
                Route::post('/user/update', 'update')->name('admin.user.update');
                Route::post('/user/{id}/uploadUpdate', 'uploadUpdate')->name('admin.user.uploadUpdate');
                Route::post('/user/delete', 'delete')->name('admin.user.delete');
            });

            Route::controller(LanguageController::class)->middleware(['checkpermission:Language Management'])->group(function () {
                // Admin Language Routes
                Route::get('/languages', 'index')->name('admin.language.index');
                Route::get('/language/{id}/edit', 'edit')->name('admin.language.edit');

                Route::post('/language/store', 'store')->name('admin.language.store');
                Route::post('/language/upload', 'upload')->name('admin.language.upload');

                Route::post('/language/{id}/default', 'default')->name('admin.language.default');
                Route::post('/language/{id}/delete', 'delete')->name('admin.language.delete');
                Route::post('/language/update', 'update')->name('admin.language.update');


                Route::get('/language/{id}/edit/frontend-keyword', 'editAdminFrontKeyword')->name('admin.language.edit_admin_front_keyword');
                Route::post('/language/{id}/update/keyword', 'updateAdminFrontKeyword')->name('admin.language.update_admin_front_keyword');
                Route::get('/language/{id}/edit/admin-keyword', 'editAdminDashboardKeyword')->name('admin.language.edit_admin_dashboard_keyword');
                Route::post('/language/{id}/update/admin-keyword', 'updateAdminDashboardKeyword')->name('admin.language.update_admin_dashboard_keyword');
                Route::get('/language/{id}/edit/user-dashboard-keyword', 'editUserDashboardKeyword')->name('admin.language.edit_user_dashboard_keyword');
                Route::post('/language/{id}/update/user-dashboard-keyword', 'updateUserDashboardKeyword')->name('admin.language.update_user_dashboard_keyword');
                Route::get('/language/{id}/edit/customer-keyword', 'editUserFrontendKeyword')->name('admin.language.edit_user_frontend_keyword');
                Route::post('/language/{id}/update/customer/keyword', 'updateUserFrontendKeyword')->name('admin.language.update_user_frontend_keyword');
            });

            // Admin Cache Clear Routes
            Route::get('/cache-clear', 'Admin\CacheController@clear')->name('admin.cache.clear');

            Route::group(['middleware' => 'checkpermission:Packages'], function () {
                // Package Settings routes
                Route::prefix('package')->controller(PackageController::class)->group(function () {

                    Route::get('/settings', 'settings')->name('admin.package.settings');
                    Route::post('/settings', 'updateSettings')->name('admin.package.settings');
                    // Package Settings routes
                    Route::get('/features', 'features')->name('admin.package.features');
                    Route::post('/features', 'updateFeatures')->name('admin.package.features');
                    // Package routes
                    Route::get('packages', 'index')->name('admin.package.index');
                    Route::post('/upload', 'upload')->name('admin.package.upload');
                    Route::post('/store', 'store')->name('admin.package.store');
                    Route::get('/{id}/edit', 'edit')->name('admin.package.edit');
                    Route::post('/update', 'update')->name('admin.package.update');
                    Route::post('/{id}/uploadUpdate', 'uploadUpdate')->name('admin.package.uploadUpdate');
                    Route::post('/delete', 'delete')->name('admin.package.delete');
                    Route::post('/bulk-delete', 'bulkDelete')->name('admin.package.bulk.delete');
                });
                // Admin Coupon Routes
                Route::prefix('coupon')->controller(CouponController::class)->group(function () {

                    Route::get('/', 'index')->name('admin.coupon.index');
                    Route::post('/store', 'store')->name('admin.coupon.store');
                    Route::get('/{id}/edit', 'edit')->name('admin.coupon.edit');
                    Route::post('/update', 'update')->name('admin.coupon.update');
                    Route::post('/delete', 'delete')->name('admin.coupon.delete');
                });
                // Admin Coupon Routes End
            });

            Route::group(['middleware' => 'checkpermission:Payment Log'], function () {
                // Payment Log
                Route::get('/payment-log', 'Admin\PaymentLogController@index')->name('admin.payment-log.index');
                Route::post('/payment-log/update', 'Admin\PaymentLogController@update')->name('admin.payment-log.update');
            });

            // Custom Domains
            Route::controller(CustomDomainController::class)->middleware('checkpermission:Custom Domains')->group(function () {
                Route::get('/domains', 'index')->name('admin.custom-domain.index');
                Route::get('/domain/texts', 'texts')->name('admin.custom-domain.texts');
                Route::post('/domain/texts', 'updateTexts')->name('admin.custom-domain.texts');
                Route::post('/domain/status', 'status')->name('admin.custom-domain.status');
                Route::post('/domain/mail', 'mail')->name('admin.custom-domain.mail');
                Route::post('/domain/delete', 'delete')->name('admin.custom-domain.delete');
                Route::post('/domain/bulk-delete', 'bulkDelete')->name('admin.custom-domain.bulk.delete');
            });

            // Subdomains
            Route::controller(SubdomainController::class)->middleware('checkpermission:Subdomains')->group(function () {
                Route::get('/subdomains', 'index')->name('admin.subdomain.index');
                Route::post('/subdomain/status', 'status')->name('admin.subdomain.status');
                Route::post('/subdomain/mail', 'mail')->name('admin.subdomain.mail');
            });

            // support-ticket route start
            Route::prefix('/support-tickets')->group(function () {
                Route::get('/settings', [SupportTicketController::class, 'settings'])->name('admin.support_tickets.settings');

                Route::post('/update-settings', [SupportTicketController::class, 'updateSettings'])->name('admin.support_tickets.update_settings');

                Route::get('/', [SupportTicketController::class, 'tickets'])->name('admin.support_tickets');

                Route::prefix('/ticket/{id}')->group(function () {


                    Route::get('/conversation', 'Admin\SupportTicketController@conversation')->name('admin.support_ticket.conversation');

                    Route::post('/close', 'Admin\SupportTicketController@close')->name('admin.support_ticket.close');

                    Route::post('/reply', 'Admin\SupportTicketController@reply')->name('admin.support_ticket.reply');

                    Route::post('/delete', 'Admin\SupportTicketController@destroy')->name('admin.support_ticket.delete');
                });

                Route::post('/bulk-delete', 'Admin\SupportTicketController@bulkDestroy')->name('admin.support_tickets.bulk_delete');

                Route::post('/store-temp-file', 'User\SupportTicketController@storeTempFile')->name('admin.support_tickets.store_temp_file');
            });
            // support-ticket route end
        });
    });

    require base_path('routes/web.php');
    require base_path('routes/tenant.php');
});
