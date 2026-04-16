<?php

namespace App\Http\Controllers;

use App\Models\User\Language;
use App\Models\User\BasicSetting;
use App\Models\User\PageHeading;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCurrencyInfo()
    {
        $baseCurrencyInfo = BasicSetting::where('user_id', Auth::guard('web')->user()->id)->select('base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate')
            ->first();

        return $baseCurrencyInfo;
    }

  
    public function getUserPageHeading($language, $userId)
    {
        if (URL::current() == Route::is('front.user.courses')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('courses_page_title')->first();
        } else if (URL::current() == Route::is('front.user.course.details')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('course_details_page_title')->first();
        } else if (URL::current() == Route::is('front.user.instructors')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('instructors_page_title')->first();
        } else if (URL::current() == Route::is('front.user.blogs')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('blog_page_title')->first();
        } else if (URL::current() == Route::is('front.user.blog_details')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('blog_details_page_title')->first();
        } else if (URL::current() == Route::is('front.user.faq')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('faq_page_title')->first();
        } else if (URL::current() == Route::is('front.user.contact')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('contact_page_title')->first();
        } else if (URL::current() == Route::is('customer.login')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('login_page_title')->first();
        } else if (URL::current() == Route::is('customer.forget_password')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('forget_password_page_title')->first();
        } else if (URL::current() == Route::is('customer.signup')) {
            $pageHeading = PageHeading::query()->where('language_id', $language->id)->where('user_id', $userId)->select('signup_page_title')->first();
        }

        return $pageHeading;
    }

    public function getUserCurrencyInfo($userId)
    {
        return BasicSetting::query()
            ->where('user_id', $userId)
            ->select(
                'base_currency_symbol',
                'base_currency_symbol_position',
                'base_currency_text',
                'base_currency_text_position',
                'base_currency_rate'
            )->first();
    }

    public function getUserBreadcrumb($userId)
    {
        return BasicSetting::query()->where('user_id', $userId)->pluck('breadcrumb')->first();
    }

    
    protected function getFrontendLang($tenantId)
    {
        $langCode = session('frontend_language');
        $currentLanguage = Language::where([['user_id', $tenantId], ['code', $langCode]])->first();
        if (!$currentLanguage) {
            Language::where([['user_id', $tenantId], ['is_default', 1]])->first();
        }
        return $currentLanguage;
    }
}
