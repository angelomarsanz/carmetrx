<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\CookieAlert;
use App\Models\User\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CookieAlertController extends Controller
{

    public function cookieAlert(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = Language::where('user_id', $userId)->where('code', $request->language)->first();
        } else {
            $language = Language::query()->where('is_default', 1)->where('user_id', $userId)->first();
        }

        $CookieAlert = CookieAlert::where([['language_id', $language->id], ['user_id', Auth::guard('web')->user()->id]]);
        if ($CookieAlert->count() == 0) {
            // if CookieAlert info of that websites does not exist then create a new one
            CookieAlert::create([
                'language_id' => $language->id,
                'user_id' => Auth::guard('web')->user()->id,
                'cookie_alert_status' => 1,
                'cookie_alert_btn_text' => 'I Agree',
                'cookie_alert_text' => 'We use cookies to give you the best online experience. By continuing to browse the site you are agreeing to our use of cookies.'
            ]);
        }
        $information['data'] = $CookieAlert->first();
        return view('user.settings.cookie-alert', $information);
    }

    public function updateCookieAlert(Request $request)
    {

        $rules = [
            'cookie_alert_status' => 'required',
            'cookie_alert_btn_text' => 'required',
            'cookie_alert_text' => 'required'
        ];
        $message = [
            'cookie_alert_btn_text.required' => __('The cookie alert button text field is required.'),
        ];
        $validator = Validator::make($request->all(), $rules, $message);


        $userId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = Language::where('user_id', $userId)->where('code', $request->language)->first();
        } else {

            $language = Language::query()->where('is_default', 1)->where('user_id', $userId)->first();
        }

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        CookieAlert::where([['language_id', $language->id], ['user_id', Auth::guard('web')->user()->id]])->update([
            'cookie_alert_status' => $request->cookie_alert_status,
            'cookie_alert_btn_text' => $request->cookie_alert_btn_text,
            'cookie_alert_text' => clean($request->cookie_alert_text)
        ]);
        Session::flash('success', __('Updated successfully!'));
        return 'success';
    }
}
