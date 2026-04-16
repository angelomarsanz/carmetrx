<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Traits\FrontendLanguage;
use App\Models\Package;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\PaymentGateway;
use App\Services\Tenant\RegistrationService;
use App\Services\Tenant\ExtendPackage;

class XenditController extends Controller
{
    use FrontendLanguage;
    public function paymentProcess(Request $request, $_amount, $_title, $_success_url, $_cancel_url, $bex)
    {
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Buy Plan Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        Session::put('request', $request->all());
        $currentLang = $this->defaultLang();
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Booking End ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $paymentMethod = PaymentGateway::where('keyword', 'xendit')->first();
        $paydata = $paymentMethod->convertAutoData();
        $external_id = Str::random(10);
        $secret_key = 'Basic ' . base64_encode($paydata['secret_key'] . ':');
        $data_request = Http::withHeaders([
            'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
            'external_id' => $external_id,
            'amount' => $_amount,
            'currency' => $be->base_currency_text,
            'success_redirect_url' => $_success_url
        ]);
        $response = $data_request->object();
        $response = json_decode(json_encode($response), true);

        if (!empty($response['success_redirect_url'])) {
            $request->session()->put('xendit_id', $response['id']);
            $request->session()->put('secret_key', $secret_key);
          
            return redirect($response['invoice_url']);
        } else {
            return redirect($_cancel_url);
        }
    }

    // return to success page
    public function successPayment(Request $request)
    {
        
        $requestData = Session::get('request');

        $currentLang = $this->defaultLang();
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $cancel_url = Session::get('cancel_url');
        /** Get the payment ID before session clear **/

        $xendit_id = Session::get('xendit_id');
        $secret_key = Session::get('secret_key');
        $paymentMethod = PaymentGateway::where('keyword', 'xendit')->first();
        $paydata = $paymentMethod->convertAutoData();
        $p_secret_key = 'Basic ' . base64_encode($paydata['secret_key'] . ':');
        if (!is_null($xendit_id) && $secret_key == $p_secret_key) {
            $paymentFor = Session::get('paymentFor');
            $package = Package::find($requestData['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = json_encode($request->all());
            $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
            $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
            $requestData['currency'] = $be->base_currency_text ?? "USD";
            $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
            $requestData['package_price'] = $package->price;
            $requestData['payment_method'] = 'Xendit';
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
            return redirect()->route($cancel_url);
        }
    }
}
