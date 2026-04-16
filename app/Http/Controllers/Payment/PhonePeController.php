<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Traits\FrontendLanguage;
use App\Models\Package;
use App\Http\Helpers\UserPermissionHelper;
use App\Services\Tenant\RegistrationService;
use App\Services\Tenant\ExtendPackage;

class PhonePeController extends Controller
{
    use FrontendLanguage;
    private $sandboxCheck;
    
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url, $_title, $bex)
    {
        Session::put('request', $request->all());
        Session::put('cancel_url', $_cancel_url);

        $paymentMethod = PaymentGateway::where('keyword', 'phonepe')->first();
        $paymentInfo = json_decode($paymentMethod->information, true);

        $this->sandboxCheck = $paymentInfo['sandbox_check'] ?? 0;
        $clientId = $paymentInfo['merchant_id'];
        $clientSecret = $paymentInfo['salt_key'];


        //* Here i completed 1 step which is generating access token in each request
        $accessToken = $this->getPhonePeAccessToken($clientId, $clientSecret);

        if (!$accessToken) {
            return back()->withError(__('Failed to get PhonePe access token') . '.');
        }

        return  $this->initiatePayment($accessToken, $_success_url, $_cancel_url, $_amount);
    }

    private function getPhonePeAccessToken($clientId, $clientSecret)
    {
        return Cache::remember('phonepe_access_token', 3500, function () use ($clientId, $clientSecret) {

            $tokenUrl = $this->sandboxCheck
                ? 'https://api-preprod.phonepe.com/apis/pg-sandbox/v1/oauth/token'
                : 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token';

            $response = Http::asForm()->post($tokenUrl, [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'client_version' => 1,
                'grant_type' => 'client_credentials'
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            return null;
        });
    }

    public function initiatePayment($accessToken, $successUrl, $cancelUrl, $_amount)
    {
        $baseUrl = $this->sandboxCheck
            ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
            : 'https://api.phonepe.com/apis/pg';

        $endpoint = '/checkout/v2/pay';

        // Generate a unique merchantOrderId and store it in the session
        $merchantOrderId = uniqid();
        Session::put('merchantOrderId', $merchantOrderId);
        Session::put('cancel_url', $cancelUrl);

        //here we preapare the parameter of the request 
        $payload = [
            'merchantOrderId' => $merchantOrderId,
            'amount' => intval($_amount * 100), //you have to multiply the amount by 100 to convert it to paise
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'merchantUrls' => [
                    'redirectUrl' => $successUrl,
                    'cancelUrl' => $cancelUrl
                ]
            ]
        ];

        try {
            //after preparing the parameter we send a request to create a payment link
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'O-Bearer ' . $accessToken,
            ])->post($baseUrl . $endpoint, $payload);

            $responseData = $response->json();

            //after successfully created the payment link of we redirect the user to api responsed redirectUrl
            if ($response->successful() && isset($responseData['redirectUrl'])) {
                return redirect()->away($responseData['redirectUrl']);
            } else {
                // Handle API errors
                Session::forget(['merchantOrderId', 'cancel_url']);
                return back()->with('error', 'Failed to initiate payment: ' . ($responseData['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {

            Session::forget(['merchantOrderId', 'cancel_url']);
            return response()->json([
                'success' => false,
                'code' => 'NETWORK_ERROR',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function successPayment(Request $request)
    {
        // Get all necessary data from session 
        $requestData = Session::get('request');
        $paymentFor = Session::get('paymentFor');
        $cancel_url = Session::get('cancel_url');

        $merchantOrderId = $request->input('merchantOrderId') ??
            Session::get('merchantOrderId') ??
            uniqid();

        $verificationResponse = $this->verifyOrderStatus($merchantOrderId);

        // Prepare transaction details with all relevant data
        $transactionDetails = [
            'payment_gateway' => 'PhonePe',
            'merchant_order_id' => $merchantOrderId,
            'gateway_response' => $verificationResponse,
            'request_data' => $requestData,
        ];

        $currentLang = $this->defaultLang();

        $be = $currentLang->basic_extended;
        $bs = $currentLang->basic_setting;

        if ($verificationResponse['success']) {
            $package = Package::find($requestData['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);

            // Include merchantOrderId in the final transaction details
            $completeTransactionDetails = array_merge($transactionDetails, [
                'internal_transaction_id' => $transaction_id,
                'payment_status' => 'completed'
            ]);

            $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
            $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
            $requestData['currency'] = $be->base_currency_text ?? "USD";
            $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
            $requestData['package_price'] = $package->price;
            $requestData['payment_method'] = 'PhonePe';
            $requestData['transaction_id'] = $transaction_id;
            $requestData['package_title'] = $package->title;
            $requestData['website_title'] = $bs->website_title;
            $requestData['transaction_details'] = json_encode($completeTransactionDetails) ?? null;
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

    private function verifyOrderStatus($merchantOrderId)
    {
        $paymentMethod = PaymentGateway::where('keyword', 'phonepe')->first();
        $paymentInfo = json_decode($paymentMethod->information, true);
        $this->sandboxCheck = $paymentInfo['sandbox_check'] ?? 0;

        // $clientId = 'TEST-M2246YU2T4XSL_25051';
        // $clientSecret = 'ZjczZTA1OWMtZjkxYS00ZjJhLTgxMjItNDdkZTNlNmUyYzhi';

        try {

            $accessToken = $this->getPhonePeAccessToken(
                $paymentInfo['merchant_id'],
                $paymentInfo['salt_key']
            );

            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            $baseUrl = $this->sandboxCheck
                ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
                : 'https://api.phonepe.com/apis/pg';

            $endpoint = "/checkout/v2/order/{$merchantOrderId}/status";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'O-Bearer ' . $accessToken,
            ])->get($baseUrl . $endpoint);

            if ($response->successful()) {
                $responseData = $response->json();

                return [
                    'success' => true,
                    'state' => $responseData['state'] ?? null,
                    'amount' => $responseData['amount'] ?? null,
                    'data' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json() ?? 'Unknown error'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function cancelPayment()
    {
        $requestData = Session::get('request');
        $paymentFor = Session::get('paymentFor');

        // Clear all payment-related session data
        $this->clearPaymentSession();

        session()->flash('warning', __('cancel_payment'));

        if ($paymentFor == "membership") {
            return redirect()->route('front.register.view', ['status' => $requestData['package_type'], 'id' => $requestData['package_id']])->withInput($requestData);
        } else {
            return redirect()->route('user.plan.extend.checkout', ['package_id' => $requestData['package_id']])->withInput($requestData);
        }
    }

    /**
     * Clear all session data related to the payment process
     */
    protected function clearPaymentSession()
    {
        Session::forget([
            'request',
            'paymentFor',
            'merchantOrderId',
            'cancel_url',
        ]);
    }
}
