<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Traits\FrontendLanguage;
use App\Models\Package;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\PaymentGateway;
use App\Services\Tenant\RegistrationService;
use App\Services\Tenant\ExtendPackage;
use Basel\MyFatoorah\MyFatoorah;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MyFatoorahController extends Controller
{
    use FrontendLanguage;
    public $myfatoorah;

    public function __construct()
    {
        $currentLang = $this->defaultLang();
        $be = $currentLang->basic_extended;

        $paymentMethod = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $paydata = $paymentMethod->convertAutoData();

        // Use the URL from your .env or fallback to the route
        $callbackUrl = env('MYFATOORAH_CALLBACK_URL', route('myfatoorah.success'));
        $errorUrl = env('MYFATOORAH_ERROR_URL', route('myfatoorah.cancel'));

        Config::set('myfatorah.token', $paydata['token']);
        Config::set('myfatorah.DisplayCurrencyIso', $be->base_currency_text);
        Config::set('myfatorah.CallBackUrl', $callbackUrl);
        Config::set('myfatorah.ErrorUrl', $errorUrl);
        if ($paydata['sandbox_status'] == 1) {
            $this->myfatoorah = MyFatoorah::getInstance(true);
        } else {
            $this->myfatoorah = MyFatoorah::getInstance(false);
        }
    }

    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url, $_title, $bex)
    {
        Log::info('Payment Process Started', [
            'request' => $request->all(),
            'success_url' => $_success_url,
            'cancel_url' => $_cancel_url,
            'config_callback' => config('myfatorah.CallBackUrl'),
            'config_error' => config('myfatorah.ErrorUrl')
        ]);

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Buy Plan Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        Session::put('request', $request->all());

        $callbackUrl = config('myfatorah.CallBackUrl', route('myfatoorah.success'));
        $errorUrl = config('myfatorah.ErrorUrl', route('myfatoorah.cancel'));

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $paymentMethod = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $paydata = $paymentMethod->convertAutoData();
        $random_1 = rand(999, 9999);
        $random_2 = rand(9999, 99999);
        $paymentFor = Session::get('paymentFor');
        $name = $paymentFor == 'membership' ? $request->first_name . ' ' . $request->last_name : auth()->user()->first_name . ' ' . auth()->user()->first_name;

        $phone = $paymentFor == 'membership' ? $request->phone : auth()->user()->phone;
        $result = $this->myfatoorah->sendPayment(
            $name,
            $_amount,
            [
                'CustomerMobile' => $paydata['sandbox_status'] == 1 ? '56562123544' : $phone,
                'CustomerReference' => "$random_1",  //orderID
                'UserDefinedField' => "$random_2", //clientID
                "InvoiceItems" => [
                    [
                        "ItemName" => "Package Purchase",
                        "Quantity" => 1,
                        "UnitPrice" => $_amount
                    ]
                    ],
                // Explicitly set the callback URLs
                'CallBackUrl' => $callbackUrl,
                'ErrorUrl' => $errorUrl
            ]
        );
        
        if ($result && $result['IsSuccess'] == true) {
            $request->session()->put('myfatoorah_payment_type', 'buy_plan');
            return redirect($result['Data']['InvoiceURL']);
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
        /** Get the payment ID before session clear **/

        if (!empty($request->paymentId)) {
            $result = $this->myfatoorah->getPaymentStatus('paymentId', $request->paymentId);
            if ($result && $result['IsSuccess'] == true && $result['Data']['InvoiceStatus'] == "Paid") {
                $paymentFor = Session::get('paymentFor');
                $package = Package::find($requestData['package_id']);
                $transaction_id = UserPermissionHelper::uniqidReal(8);
                $transaction_details = json_encode($request->all());

                $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
                $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
                $requestData['currency'] = $be->base_currency_text ?? "USD";
                $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
                $requestData['package_price'] = $package->price;
                $requestData['payment_method'] = 'MyFatoorah';
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
                    return [
                        'status' => 'success'
                    ];
                } elseif ($paymentFor == "extend") {

                    $requestData['is_trial'] =   0;
                    $requestData['trial_days'] =  0;
                    $tenantExtendedPackage = new ExtendPackage();
                    $tenantExtendedPackage->exdendPackage($requestData);

                    session()->flash('success', __('successful_payment'));
                    Session::forget('request');
                    Session::forget('paymentFor');
                    return [
                        'status' => 'success'
                    ];
                }
            } else {
                return [
                    'status' => 'fail'
                ];
            }
        }
    }
}
