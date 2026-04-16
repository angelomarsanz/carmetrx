<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\Package;
use PHPMailer\PHPMailer\Exception;
use App\Services\PaymentGateway\StripeService;
use Illuminate\Support\Facades\Session;
use App\Services\Tenant\ExtendPackage;
use App\Services\Tenant\RegistrationService;
use App\Traits\FrontendLanguage;

class StripeController extends Controller
{
    use FrontendLanguage;
    private $stripePaymentService;

    public function __construct()
    {
        $this->stripePaymentService = new StripeService();
    }

    public function paymentProcess(Request $request, $_amount, $_title, $_success_url, $_cancel_url, $webCuryency)
    {

        $validCurrency = $this->stripePaymentService->checkCurrency($webCuryency, array("USD"));

        if (!$validCurrency) {
            return redirect()->back()->with('error', __('Currency is not valid for this payment gateway') . '.')->withInput($request->all());
        }
        $title = $_title;
        $amount = $_amount;
        $cancel_url = $_cancel_url;

        Session::put('request', $request->all());

        try {
            $this->stripePaymentService->setCredentials(true);
            $token = $this->stripePaymentService->createToken([
                'number' => $request->cardNumber,
                'exp_month' => $request->month,
                'exp_year' => $request->year,
                'cvc' => $request->cardCVC,
            ]);

            if (!isset($token['id'])) {
                return back()->with('error', __('Token Problem With Your Token') . '.');
            }

            $charge = $this->stripePaymentService->createCharge($token['id'], $amount, 'USD', $title);


            if ($charge['status'] == 'succeeded') {

                $paymentFor = Session::get('paymentFor');
                $requestData = Session::get('request');

                $currentLang = $this->defaultLang();
                $be = $currentLang->basic_extended;
                $bs = $currentLang->basic_setting;
                $package = Package::find($requestData['package_id']);
                $transaction_id = UserPermissionHelper::uniqidReal(8);
                $transaction_details = json_encode($charge);
                $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
                $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
                $requestData['currency'] = $be->base_currency_text ?? "USD";
                $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
                $requestData['package_price'] = $package->price;
                $requestData['payment_method'] = 'Stripe';
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
        } catch (Exception $e) {
            return redirect($cancel_url)->with('error', $e->getMessage());
        } catch (\Cartalyst\Stripe\Exception\CardErrorException $e) {
            return redirect($cancel_url)->with('error', $e->getMessage());
        } catch (\Cartalyst\Stripe\Exception\MissingParameterException $e) {
            return redirect($cancel_url)->with('error', $e->getMessage());
        }
        return redirect($cancel_url)->with('error', __('Please Enter Valid Credit Card Informations') . '.');
    }

    public function cancelPayment()
    {
        $requestData = Session::get('request');
        $paymentFor = Session::get('paymentFor');
        session()->flash('error', __('cancel_payment'));
        if ($paymentFor == "membership") {
            return redirect()->route('front.register.view', ['status' => $requestData['package_type'], 'id' => $requestData['package_id']])->withInput($requestData);
        } else {
            return redirect()->route('user.plan.extend.checkout', ['package_id' => $requestData['package_id']])->withInput($requestData);
        }
    }
}
