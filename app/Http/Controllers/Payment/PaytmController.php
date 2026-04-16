<?php

namespace App\Http\Controllers\Payment;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\PaytmService;
use Illuminate\Support\Facades\Session;
use App\Services\Tenant\ExtendPackage;
use App\Services\Tenant\RegistrationService;
use App\Traits\FrontendLanguage;

class PaytmController extends Controller
{
    use FrontendLanguage;
    private $paytmPaymentService;

    public function __construct()
    {
        $this->paytmPaymentService = new PaytmService();
    }

    public function paymentProcess(Request $request, $_amount, $_item_number, $_callback_url, $webCuryency)
    {
        $validCurrency = $this->paytmPaymentService->checkCurrency($webCuryency, array("INR"));

        if (!$validCurrency) {
            return redirect()->back()->with('error', __('Currency is not valid for this payment gateway') . '.')->withInput($request->all());
        }

        Session::put("request", $request->all());
        $this->paytmPaymentService->setCredentials(true);
        return $this->paytmPaymentService->renderPaytmView($_amount, $_item_number, $_callback_url);
    }

    public function paymentStatus(Request $request)
    {
        $paymentFor = Session::get('paymentFor');
        $requestData = Session::get('request');
        if ($request["STATUS"] === "TXN_FAILURE") {
            $paymentFor = Session::get('paymentFor');
            session()->flash('warning', __('cancel_payment'));

            if ($paymentFor == "membership") {
                return redirect()->route('front.register.view', ['status' => $requestData['package_type'], 'id' => $requestData['package_id']])->withInput($requestData);
            } else {
                return redirect()->route('user.plan.extend.checkout', ['package_id' => $requestData['package_id']])->withInput($requestData);
            }
        } elseif ($request['STATUS'] === 'TXN_SUCCESS') {



            $currentLang = $this->defaultLang();
            $be = $currentLang->basic_extended;
            $bs = $currentLang->basic_setting;
            $package = Package::find($requestData['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = json_encode($request);
            $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
            $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
            $requestData['currency'] = $be->base_currency_text ?? "USD";
            $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
            $requestData['package_price'] = $package->price;
            $requestData['payment_method'] = 'Paytm';
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
    }
}
