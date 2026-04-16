<?php

use App\Http\Controllers\CronJobController;
use App\Http\Controllers\Front\FrontendController;
use App\Http\Controllers\User\Auth\ForgotPasswordController;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/changelanguage/{lang}', [FrontendController::class, 'changeLanguage'])->name('changeLanguage');
// cron job for sending expiry mail

Route::get('/subcheck', [CronJobController::class, 'expired'])->name('cron.expired');
Route::get('/check-payment', [CronJobController::class, 'checkPayment'])->name('cron.check_payment');

Route::controller(FrontendController::class)->middleware(['setlang'])->group(function () {
  Route::get('/', 'index')->name('front.index');

  Route::post('/subscribe', 'subscribe')->name('front.subscribe');
  Route::get('/about', 'aboutUs')->name('front.user.aboutus');
  Route::get('/listings', 'users')->name('front.user.view');
  Route::get('/contact', 'contactView')->name('front.contact');
  Route::get('/faqs', 'faqs')->name('front.faq.view');
  Route::get('/blog', 'blogs')->name('front.blogs');
  Route::get('/pricing', 'pricing')->name('front.pricing');
  Route::get('/blog-details/{slug}/{id}', 'blogdetails')->name('front.blogdetails');
  Route::get('/registration/step-1/{status}/{id}', 'step1')->name('front.register.view');
  Route::get('/check/{username}/username', 'checkUsername')->name('front.username.check');
  Route::get('/page/{slug}', 'dynamicPage')->name('front.dynamicPage');
  Route::view('/success', 'front.success')->name('success.page');
  Route::post('/contact-msg', 'adminContactMessage')->name('front.admin.contact.message');
});


Route::middleware(['web', 'guest', 'setlang'])->group(function () {
  Route::get('/registration/final-step', 'Front\FrontendController@step2')->name('front.registration.step2');
  Route::post('/checkout', 'Front\FrontendController@checkout')->name('front.checkout.view');

  Route::get('/login', [LoginController::class, 'showLoginForm'])->name('user.login');
  Route::post('/login', [LoginController::class, 'login'])->name('user.login.submit');

  Route::get('/register/mode/{mode}/verify/{token}', [RegisterController::class, 'token'])->name('user-register-token');

  Route::post('/password/email', 'User\Auth\ForgotPasswordController@sendResetLinkEmail')->name('user.forgot.password.submit');
  Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('user.forgot.password.form');
  Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('user.reset.password.submit');
  Route::get('/password/reset/{token}/email/{email}', [ResetPasswordController::class, 'showResetForm'])->name('user.reset.password.form');

  Route::get('/forgot', 'User\ForgotController@showforgotform')->name('user-forgot');
  Route::post('/forgot',  'User\ForgotController@forgot')->name('user-forgot-submit');
});
