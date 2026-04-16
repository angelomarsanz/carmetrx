<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\Package;
use App\Models\PaymentGateway;
use App\Services\Tenant\ExtendPackage;
use App\Services\Tenant\RegistrationService;
use App\Traits\FrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Midtrans\Snap;
use Midtrans\Config as MidtransConfig;

class MidtransController extends Controller
{
    use FrontendLanguage;
    public function paymentProcess(Request $request, $_amount, $success_url, $_cancel_url)
    {
        Session::put('request', $request->all());
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

        $paymentFor = Session::get('paymentFor');
        $data = [];
        if ($paymentFor == "membership") {
            $name = $request->first_name . ' ' . $request->last_name;
            $email = $request->email;
            $phone = $request->phone;
            $data['title'] = 'Package Purchase via Midtrans';
        } else {;
            $name = Auth::guard('web')->user()->first_name . ' ' . Auth::guard('web')->user()->last_name;
            $email =  Auth::guard('web')->user()->email;
            $phone = Auth::guard('web')->user()->phone;
            $data['title'] = 'Package Extends via Midtrans';
        }

        $paymentMethod = PaymentGateway::where('keyword', 'midtrans')->first();
        $paydata = json_decode($paymentMethod->information, true);
        // dd($paydata['server_key']);
        // will come from database
        MidtransConfig::$serverKey = $paydata['server_key'];
        MidtransConfig::$isProduction = $paydata['is_production'] == 0 ? true : false;
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
        $token = uniqid();
        Session::put('token', $token);
        $params = [
            'transaction_details' => [
                'order_id' => $token,
                'gross_amount' => $_amount
            ],
            'customer_details' => [
                'first_name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
        ];

        // dd($params);

        $snapToken = Snap::getSnapToken($params);

        // dd($snapToken);

        // put some data in session before redirect to midtrans url
        if (
            $paydata['is_production'] == 1
        ) {
            $is_production = $paydata['is_production'];
        }
        $success_url = route('membership.midtrans.success');
        $data['snapToken'] = $snapToken;
        $data['is_production'] = $is_production;
        $data['success_url'] = $success_url;
        $data['_cancel_url'] = $_cancel_url;
        $data['client_key'] = $paydata['server_key'];
        Session::put('midtrans_payment_type', 'membership');
        return view('payment.midtrans', $data);
    }


    public function successPayment(Request $request)
    {
        $requestData = Session::get('request');

        $requestData['status'] = 1;

        $currentLang = $this->defaultLang();
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        /** clear the session payment ID **/
        $cancel_url = route('membership.midtrans.payment.cancel');

        $token = Session::get('token');
        // if ($request->status_code == 200 && $token == $request->order_id) {
        $paymentFor = Session::get('paymentFor');
        $package = Package::find($requestData['package_id']);
        $transaction_id = UserPermissionHelper::uniqidReal(8);
        $transaction_details = json_encode($request->all());

        $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
        $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
        $requestData['currency'] = $be->base_currency_text ?? "USD";
        $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
        $requestData['package_price'] = $package->price;
        $requestData['payment_method'] = 'Midtrans';
        $requestData['transaction_id'] = $transaction_id;
        $requestData['package_title'] = $package->title;
        $requestData['website_title'] = $bs->website_title;
        $requestData['transaction_details'] = $transaction_details ?? null;
        $requestData['settings'] =  json_encode($be);
        $transaction_details = null;
        if ($paymentFor == "membership") {

            $requestData['is_trial'] = $requestData["package_type"] == "regular" ? 0 : 1;
            $requestData['trial_days'] = $requestData["package_type"] == "regular" ? 0 : $requestData["trial_days"];
            $tenantRegistration = new RegistrationService();
            $tenantRegistration->register($requestData);

            session()->flash('success', __('successful_payment'));
            Session::forget('request');
            Session::forget('paymentFor');
            return redirect()->route('success.page');
        } elseif ($paymentFor == "extend") {

            $requestData['is_trial'] =   0;
            $requestData['trial_days'] =  0;
            $tenantExtendedPackage = new ExtendPackage();
            $tenantExtendedPackage->exdendPackage($requestData);

            session()->flash('success', __('successful_payment'));
            Session::forget('request');
            Session::forget('paymentFor');
            return redirect()->route('success.page');
        }
        // }
        return redirect($cancel_url);
    }
}
