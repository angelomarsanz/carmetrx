<?php

use App\Http\Controllers\UserFrontend\AgentController;
use App\Http\Controllers\UserFrontend\ContactController;
use App\Http\Controllers\UserFrontend\HomePageController;
use App\Http\Controllers\UserFrontend\MiscellaneousController;
use App\Http\Controllers\UserFrontend\ProjectController;
use App\Http\Controllers\UserFrontend\PropertyController;
use App\Http\Controllers\UserFrontend\UserController;
use Illuminate\Support\Facades\Route;

$domain = env('WEBSITE_HOST');
$parsedUrl = parse_url(url()->current());

$host = str_replace("www.", "", $parsedUrl['host']);
if (array_key_exists('host', $parsedUrl)) {
    // if it is a path based URL
    if ($host == env('WEBSITE_HOST')) {
        $domain = $domain;
        $prefix = '/{username}';
    }
    // if it is a subdomain / custom domain
    else {
        if (!app()->runningInConsole()) {
            if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.') {
                $domain = 'www.{domain}';
            } else {
                $domain = '{domain}';
            }
        }
        $prefix = '';
    }
}

Route::group(['domain' => $domain, 'prefix' => $prefix, 'middleware' => 'userMaintenance'], function () use ($domain, $prefix) {
    Route::middleware(['frontend.language'])->name('frontend.')->group(function () {


        Route::get('/change-language', [MiscellaneousController::class, 'changeLanguage'])->name('change_language');

        Route::post('/store-subscriber', [MiscellaneousController::class, 'storeSubscriber'])->name('store_subscriber');


        Route::get('/', [HomePageController::class, 'index'])->name('user.index');
        Route::get('/about-us', [HomePageController::class, 'aboutus'])->name('aboutus');

        // Properties route
        Route::controller(PropertyController::class)->middleware(['TFRAcessPermission:Property Management'])->group(function () {
            Route::get('/properties', 'index')->name('properties');
            Route::get('/property/{slug}', 'details')->name('property.details');
            Route::post('/property-contact', 'contact')->name('property_contact');
            Route::get('/state-cities', 'getStateCities')->name('get_state_cities');
            Route::get('/cities', 'getCities')->name('get_cities');
            Route::get('/categories', 'getCategories')->name('get_categories');
        });
        // Projects route
        Route::controller(ProjectController::class)->middleware(['TFRAcessPermission:Project Management'])->group(function () {
            Route::get('/projects', 'index')->name('projects');
            Route::get('/project/{slug}', 'details')->name('project.details');
            Route::get('/project-categories', 'getCategories')->name('project.get_categories');
            Route::get('/project-state-cities', 'getStateCities')->name('project.get_state_cities');
            Route::get('/project-cities', 'getCities')->name('project.get_cities');
            Route::post('/project-contact', 'contact')->name('project.contact');
        });

        // Projects route
        Route::controller(AgentController::class)->middleware(['TFRAcessPermission:Agent'])->group(function () {
            Route::get('/team', 'index')->name('agents');
            Route::get('/tenant-details', 'tenantDetails')->name('tenant.details');
            Route::get('/team-details/{agentusername}', 'details')->name('agent.details');
            Route::get('/team/login', 'login')->name('agent.login');
            Route::get('/team/forget-password', 'forget_passord')->name('agent.forget.password');
            Route::post('/send-forget-mail', 'forget_mail')->name('agent.forget.mail');
            Route::get('/team/reset-password', 'reset_password')->name('agent.reset.password');
            Route::post('/team/update-forget-password', 'update_password')->name('agent.update-forget-password');
            Route::post('/contact-mail', 'contactAgent')->name('contact_agent');
        });


        Route::get('/payment-form', 'UserFrontend\PayController@index')->name('payment_form');


        Route::prefix('/blog')->middleware(['TFRAcessPermission:Blog'])->group(function () {
            Route::get('', 'UserFrontend\BlogController@index')->name('blog');

            Route::get('/post/{slug}', 'UserFrontend\BlogController@show')->name('blog.post_details');
        });

        Route::get('/faq', 'UserFrontend\FaqController@faq')->name('faq');

        Route::prefix('/contact')->controller(ContactController::class)->group(function () {
            Route::get('', 'contact')->name('contact');

            Route::post('/send-mail', 'sendMail')->name('contact.send_mail')->withoutMiddleware('change.lang');
        });


        Route::prefix('/user')->middleware(['guest:customer', 'TFRAcessPermission:User'])->group(function () {
            Route::prefix('/login')->controller(UserController::class)->group(function () {
                // user redirect to login page route
                Route::get('', 'login')->name('user.login');



                ///// user login via google route
                Route::prefix('/google')->group(function () {
                    Route::get('', 'redirectToGoogle')->name('user.login.google');
                    Route::get('/callback', 'handleGoogleCallback')->name('user.login.google_callback');
                });
            });

            // user login submit route
            Route::post('/login-submit', 'UserFrontend\UserController@loginSubmit')->name('user.login_submit')->withoutMiddleware(['change.lang']);

            // user forget password route
            Route::get('/forget-password', 'UserFrontend\UserController@forgetPassword')->name('user.forget_password');

            // send mail to user for forget password route
            Route::post('/send-forget-password-mail', 'UserFrontend\UserController@forgetPasswordMail')->name('user.send_forget_password_mail')->withoutMiddleware('change.lang');

            // reset password route
            Route::get('/reset-password/{token}', 'UserFrontend\UserController@resetPassword')->name('user.reset_password');

            // user reset password submit route
            Route::post('/reset-password-submit', 'UserFrontend\UserController@resetPasswordSubmit')->name('user.reset_password_submit')->withoutMiddleware('change.lang');

            // user redirect to signup page route
            Route::get('/signup', 'UserFrontend\UserController@signup')->name('user.signup');

            // user signup submit route
            Route::post('/signup-submit', 'UserFrontend\UserController@signupSubmit')->name('user.signup_submit')->withoutMiddleware('change.lang');

            // signup verify route
            Route::get('/signup-verify/{token}', 'UserFrontend\UserController@signupVerify')->name('user.verify_email')->withoutMiddleware('change.lang');
        });

        Route::prefix('/user')->controller(UserController::class)->middleware(['auth:customer', 'account.status', 'TFRAcessPermission:User'])->group(function () {
            // user redirect to dashboard route
            Route::get('/dashboard', 'redirectToDashboard')->name('user.dashboard');
            Route::get('/property-wishlist', 'propertyWishlist')->name('user.property.wishlist');
            Route::get('addto/wishlist/{property}', 'addToWishlist')->name('user.property.addto.wishlist');
            Route::get('remove/wishlist/{property}', 'removeWishlist')->name('user.property.remove.wishlist');
            Route::get('/project-wishlist', 'projectWishlist')->name('user.project.wishlist');
            Route::get('addto/project-wishlist/{project}', 'addToProjectWishlist')->name('user.project.addto.wishlist');
            Route::get('remove/project-wishlist/{project}', 'removeProjectWishlist')->name('user.project.remove.wishlist');

            // edit profile route
            Route::get('/edit-profile', 'editProfile')->name('user.edit_profile');

            // update profile route
            Route::post('/update-profile', 'updateProfile')->name('user.update_profile')->withoutMiddleware('change.lang');

            Route::middleware('exists.password')->group(function () {
                // change password route
                Route::get('/change-password', 'changePassword')->name('user.change_password');

                // update password route
                Route::post('/update-password', 'updatePassword')->name('user.update_password')->withoutMiddleware('change.lang');
            });


            // support tickets route
            Route::prefix('/support-tickets')->group(function () {
                Route::get('', 'UserFrontend\UserController@tickets')->name('user.support_tickets');

                Route::get('/create-ticket', 'UserFrontend\UserController@createTicket')->name('user.support_tickets.create');

                Route::post('/store-temp-file', 'UserFrontend\UserController@storeTempFile')->name('user.support_tickets.store_temp_file');

                Route::post('/store-ticket', 'UserFrontend\UserController@storeTicket')->name('user.support_tickets.store');
            });

            Route::get('/support-ticket/{id}/conversation', 'UserFrontend\UserController@ticketConversation')->name('user.support_ticket.conversation');

            Route::post('/support-ticket/{id}/reply', 'UserFrontend\UserController@ticketReply')->name('user.support_ticket.reply');

            // user logout attempt route
            Route::get('/logout', 'UserFrontend\UserController@logoutSubmit')->name('user.logout')->withoutMiddleware('change.lang');
        });


        /*
    |--------------------------------------------------------------------------
    | Additional Page Route For UI
    |--------------------------------------------------------------------------
    */
        Route::get('/{slug}', 'UserFrontend\PageController@page')->middleware(['TFRAcessPermission:Additional Page'])->name('dynamic_page');
    });
    require base_path('routes/agent.php');
});
