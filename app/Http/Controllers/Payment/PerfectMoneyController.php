<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Traits\FrontendLanguage;
use App\Models\Package;
use App\Http\Helpers\UserPermissionHelper;
use App\Services\Tenant\RegistrationService;
use App\Services\Tenant\ExtendPackage;

class PerfectMoneyController extends Controller
{
    use FrontendLanguage;
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url, $_title, $bex)
    {

        // $_amount = 0.01; //if test
        $currentLang = $this->defaultLang();
        $bs = $currentLang->basic_setting;
        Session::put('request', $request->all());
        $paymentMethod = PaymentGateway::where('keyword', 'perfect_money')->first();
        $paydata = $paymentMethod->convertAutoData();
        $notify_url = $_success_url;
        $randomNo = substr(uniqid(), 0, 8);

        $val['PAYEE_ACCOUNT'] = $paydata['perfect_money_wallet_id'];;
        $val['PAYEE_NAME'] = $bs->website_title;
        $val['PAYMENT_ID'] = "$randomNo"; //random id
        $val['PAYMENT_AMOUNT'] = $_amount;
        $val['PAYMENT_UNITS'] = "$bex->base_currency_text";

        $val['STATUS_URL'] = $_success_url;
        $val['PAYMENT_URL'] = $_success_url;
        $val['PAYMENT_URL_METHOD'] = 'GET';
        $val['NOPAYMENT_URL'] = $_cancel_url;
        $val['NOPAYMENT_URL_METHOD'] = 'GET';
        $val['SUGGESTED_MEMO'] = "$request->email";
        $val['BAGGAGE_FIELDS'] = 'IDENT';

        $data['val'] = $val;
        $data['method'] = 'POST';
        $data['url'] = 'https://perfectmoney.com/api/step1.asp';

        Session::put('payment_id', $randomNo);
        Session::put('cancel_url', $_cancel_url);
        Session::put('amount', $_amount);
        return view('payment.perfect-money', compact('data'));
    }

    public function successPayment(Request $request)
    {
        $requestData = Session::get('request');

        $currentLang = $this->defaultLang();
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $cancel_url = Session::get('cancel_url');
        /** Get the payment ID before session clear **/

        $amo = $request['PAYMENT_AMOUNT'];
        $track = $request['PAYMENT_ID'];
        $id = Session::get('payment_id');
        $final_amount = Session::get('amount');
        $paymentMethod = PaymentGateway::where('keyword', 'perfect_money')->first();
        $perfectMoneyInfo = $paymentMethod->convertAutoData();

        if ($request->PAYEE_ACCOUNT == $perfectMoneyInfo['perfect_money_wallet_id']  && $track == $id && $amo == round($final_amount, 2)) {
            $paymentFor = Session::get('paymentFor');
            $package = Package::find($requestData['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = json_encode($request->all());

            $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
            $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
            $requestData['currency'] = $be->base_currency_text ?? "USD";
            $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
            $requestData['package_price'] = $package->price;
            $requestData['payment_method'] = 'Perfect Money';
            $requestData['transaction_id'] = $transaction_id;
            $requestData['package_title'] = $package->title;
            $requestData['website_title'] = $bs->website_title;
            $requestData['transaction_details'] = $transaction_details ?? null;
            $requestData['settings'] =  json_encode($be);

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
        }
        return redirect($cancel_url);
    }
    public function cancelPayment()
    {
        $requestData = Session::get('request');
        $paymentFor = Session::get('paymentFor');
        session()->flash('warning', __('cancel_payment'));
        if ($paymentFor == "membership") {
            return redirect()->route('front.register.view', ['status' => $requestData['package_type'], 'id' => $requestData['package_id']])->withInput($requestData);
        } else {
            return redirect()->route('user.plan.extend.checkout', ['package_id' => $requestData['package_id']])->withInput($requestData);
        }
    }
}
