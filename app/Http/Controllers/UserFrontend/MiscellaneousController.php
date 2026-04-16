<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Models\User\BasicSetting;
use App\Models\User\Language;
use App\Models\User\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Session;

class MiscellaneousController extends Controller
{
    use TenantFrontendLanguage;


    public function changeLanguage(Request $request)
    {
        $language = $this->getLanguageInstance();
        $langCode = $request['lang_code'];
        $request->session()->put('tenant_frontend_lang', $langCode);
        $language = $language->where([['user_id', getUser()->id], ['code', $langCode]])->select('id', 'user_id', 'name', 'is_default', 'rtl', 'code')->first();
        session()->forget('tenant_frontend_lang');
        session()->put('tenant_frontend_lang', $language->code);
        app()->setLocale($language->code);
        return back();
    }


    public static function getBreadcrumb($tenantId)
    {
        $breadcrumb = BasicSetting::where('user_id', $tenantId)->pluck('breadcrumb')->first();

        return $breadcrumb;
    }

    public function getFrontCurrencyInfo($userId)
    {
        $baseCurrencyInfo = BasicSetting::where('user_id', $userId)->select('base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate')
            ->first();

        return $baseCurrencyInfo;
    }

    public function storeSubscriber(Request $request)
    {
        $userId = getUser()->id;
        $request->validate([
            'email' => ['required', 'email:rfc,dns',  Rule::unique('user_subscribers')->where(function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })]
        ], [
            'email.required' => 'Please enter your email address.',
            'email.unique' => 'This email address is already exist!'
        ]);

        Subscriber::query()->create([
            'user_id' => $userId,
            'email' => $request->email
        ]);
        Session::flash('success', __('You have successfully subscribed to our newsletter.'));

        return redirect()->back();
    }
}
