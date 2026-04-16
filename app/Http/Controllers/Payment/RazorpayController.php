<?php

namespace App\Http\Controllers\Payment;

use App\Http\Helpers\UserPermissionHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\PaymentGateway\RazorpayService;
use Illuminate\Support\Facades\Session;
use App\Services\Tenant\ExtendPackage;
use App\Services\Tenant\RegistrationService;
use App\Traits\FrontendLanguage;

class RazorpayController extends Controller
{
    use FrontendLanguage;
    private $razorPaymentService;

    public function __construct()
    {
        $this->razorPaymentService = new RazorpayService();
        $this->razorPaymentService->setCredentials(true);
    }

    public function paymentProcess(Request $request, $_amount, $_item_number, $_success_url, $_cancel_url, $_title, $_description, $bs, $webCuryency)
    {
        $validCurrency = $this->razorPaymentService->checkCurrency($webCuryency, array("INR"));

        if (!$validCurrency) {
            return redirect()->back()->with('error', __('Currency is not valid for this payment gateway') . '.')->withInput($request->all());
        }

        Session::put('request', $request->all());

        $data['name'] =  $request->name;
        $data['email'] = $request->email;
        $data['razorpay_phone'] = $request->razorpay_phone;
        $data['razorpay_address'] = $request->razorpay_address;
        $data['base_color'] = $bs->base_color;
        $data['title'] = $_title;
        $data['description'] = $_description;
        $data['base_currency_text'] = $webCuryency;

        return $this->razorPaymentService->makePayment($data, $_amount, $_item_number, $_success_url, $_cancel_url);
    }

    public function successPayment(Request $request)
    {
        $cancel_url = route('membership.razorpay.cancel');
        $verifyPayment = $this->razorPaymentService->verifyPayment($request);

        if ($verifyPayment) {

            $paymentFor = Session::get('paymentFor');
            $requestData = Session::get('request');

            $currentLang = $this->defaultLang();
            $be = $currentLang->basic_extended;
            $bs = $currentLang->basic_setting;
            $package = Package::find($requestData['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = json_encode($request);
            $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
            $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
            $requestData['currency'] = $be->base_currency_text;
            $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
            $requestData['package_price'] = $package->price;
            $requestData['payment_method'] = 'Razorpay';
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
            return redirect($cancel_url);
        }
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
