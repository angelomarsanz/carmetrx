<?php

namespace App\Http\Controllers\Payment;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\FlutterWaveService;
use Illuminate\Support\Facades\Session;
use App\Services\Tenant\ExtendPackage;
use App\Services\Tenant\RegistrationService;
use App\Traits\FrontendLanguage;

class FlutterWaveController extends Controller
{
    use FrontendLanguage;
    private $flutterWave;

    public function __construct()
    {
        $this->flutterWave = new FlutterWaveService();
        $this->flutterWave->setCredentials(true);
    }

    public function paymentProcess(Request $request, $amount, $email, $item_number, $successUrl, $cancelUrl, $webCuryency)
    {
        Session::put('request', $request->all());

        $validCurrency = $this->flutterWave->checkCurrency($webCuryency, array('BIF', 'CAD', 'CDF', 'CVE', 'EUR', 'GBP', 'GHS', 'GMD', 'GNF', 'KES', 'LRD', 'MWK', 'NGN', 'RWF', 'SLL', 'STD', 'TZS', 'UGX', 'USD', 'XAF', 'XOF', 'ZMK', 'ZMW', 'ZWD'));

        if (!$validCurrency) {
            return redirect()->back()->with('error', __('Currency is not valid for this payment gateway') . '.')->withInput($request->all());
        }
        return $this->flutterWave->makePayment($amount, $email, $successUrl, $cancelUrl, $item_number, $webCuryency);
    }

    public function successPayment(Request $request)
    {

        $cancel_url = route('membership.flutterwave.cancel');


        $urlInfo = $request->all();

        if ($urlInfo['status'] == 'successful') {
            $txId = $urlInfo['transaction_id'];


            $response = $this->flutterWave->verifyPayment($txId);


            if ($response['status'] == 'error') {
                return redirect($cancel_url);
            }
            if ($response['status'] = "success") {
                $paymentStatus = $response['data']['status'];
                $paymentFor = Session::get('paymentFor');
                if ($response['status'] = "success") {

                    $paymentFor = Session::get('paymentFor');
                    $requestData = Session::get('request');


                    $currentLang = $this->defaultLang();
                    $be = $currentLang->basic_extended;
                    $bs = $currentLang->basic_setting;

                    $package = Package::find($requestData['package_id']);
                    $transaction_id = UserPermissionHelper::uniqidReal(8);
                    $transaction_details = json_encode($response);
                    $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
                    $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
                    $requestData['currency'] = $be->base_currency_text ?? "USD";
                    $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
                    $requestData['package_price'] = $package->price;
                    $requestData['payment_method'] = 'Flutterwave';
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
            return redirect($cancel_url);
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
