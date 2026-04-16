<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Services\PaymentGateway\AuthorizenetService;
use Illuminate\Support\Facades\Session;
use App\Services\Tenant\ExtendPackage;
use App\Services\Tenant\RegistrationService;
use App\Traits\FrontendLanguage;

class AuthorizenetController extends Controller
{
    use FrontendLanguage;
    private $authorizenet;

    public function __construct()
    {
        $this->authorizenet = new AuthorizenetService();
        $this->authorizenet->setCredentials(true);
    }

    public function paymentProcess(Request $request, $amount, $_cancel_url, $_title, $webCuryency)
    {
        $validCurrency = $this->authorizenet->checkCurrency($webCuryency, array('USD', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'NOK', 'PLN', 'SEK', 'AUD', 'NZD'));

        if (!$validCurrency) {
            return redirect()->back()->with('error', __('Currency is not valid for this payment gateway') . '.')->withInput($request->all());
        }

        Session::put('request', $request->all());

        if ($request->input('opaqueDataDescriptor') && $request->input('opaqueDataValue')) {
            try {
                // Generate a unique merchant site transaction ID.
                $transactionId = rand(100000000, 999999999);

                $makePayment = $this->authorizenet->makePayment($request, $amount, $transactionId, $webCuryency);

                if ($makePayment->isSuccessful()) {

                    // Captured from the authorization response.
                    $transactionReference = $makePayment->getTransactionReference();
                    $verify = $this->authorizenet->verifyPayment($transactionReference, $amount, $webCuryency);

                    $transaction_id = $verify->getTransactionReference();

                    $paymentFor = Session::get('paymentFor');
                    $requestData = Session::get('request');


                    $currentLang = $this->defaultLang();
                    $be = $currentLang->basic_extended;
                    $bs = $currentLang->basic_setting;

                    $package = Package::find($requestData['package_id']);
                    // $transaction_id = UserPermissionHelper::uniqidReal(8);
                    $transaction_details = json_encode($verify);
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
                } else {
                    // not successful
                    $request->session()->flash('error', $makePayment->getMessage());
                    return redirect($_cancel_url);
                }
            } catch (\Exception $e) {
                $request->session()->flash('error', $e->getMessage());
                return redirect($_cancel_url);
            }
        }
    }

 
    public function cancelPayment()
    {
        $request = Session::get('request');
        $paymentFor = Session::get('paymentFor');
        if ($paymentFor == "membership") {
            return redirect()->route('front.register.view', ['status' => $request['package_type'], 'id' => $request['package_id']])->withInput($request);
        } else {
            return redirect()->route('user.plan.extend.checkout', ['package_id' => $request['package_id']])->withInput($request);
        }
    }
}
