<?php

namespace App\Http\Controllers\Payment;


use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\User\UserCheckoutController;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\Package;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\MegaMailer;
use App\Models\Language;
use App\Services\PaymentGateway\MercadopagoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Stmt\TryCatch;
use App\Services\Tenant\ExtendPackage;
use App\Services\Tenant\RegistrationService;
use App\Traits\FrontendLanguage;

class MercadopagoController extends Controller
{
    use FrontendLanguage;
    private $mercadopago;

    public function __construct()
    {
        $this->mercadopago = new MercadopagoService();
        $this->mercadopago->setCredentials(true);
    }

    public function paymentProcess(Request $request, $amount, $success_url, $cancel_url, $email, $title, $description, $webCuryency)
    {

        $validCurrency = $this->mercadopago->checkCurrency($webCuryency, array("BRL"));

        if (!$validCurrency) {
            return redirect()->back()->with('error', __('Currency is not valid for this payment gateway') . '.')->withInput($request->all());
        }
        Session::put('request', $request->all());

        return  $this->mercadopago->makePayment($title, $description, $amount, $email,  $success_url, $cancel_url);
    }


    public function paycancle()
    {
        return redirect()->back()->with('error', __('Payment Cancelled') . '.');
    }

    public function payreturn()
    {
        if (Session::has('tempcart')) {
            $oldCart = Session::get('tempcart');
            $tempcart = new Cart($oldCart);
            $order = Session::get('temporder');
        } else {
            $tempcart = '';
            return redirect()->back();
        }

        return view('front.success', compact('tempcart', 'order'));
    }

    public function successPayment(Request $request)
    {

        $cancel_url = Session::get('cancel_url');

        $paymentData = $this->mercadopago->verifyPayment($request['data']['id']);
        $payment = json_decode($paymentData, true);
        if ($payment['status'] == 'approved') {

            $transaction_details = json_encode($payment);

            $paymentFor = Session::get('paymentFor');
            $requestData = Session::get('request');


            $currentLang = $this->defaultLang();
            $be = $currentLang->basic_extended;
            $bs = $currentLang->basic_setting;

            $package = Package::find($requestData['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = json_encode($payment);
            $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
            $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
            $requestData['currency'] = $be->base_currency_text ?? "USD";
            $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
            $requestData['package_price'] = $package->price;
            $requestData['payment_method'] = 'Mercadopago';
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
