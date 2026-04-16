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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ToyyibpayController extends Controller
{
    use FrontendLanguage;
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url, $_title, $bex)
    {

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Buy Plan Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        Session::put('request', $request->all());

        $user = Auth::guard('web')->user();

        // Resolve input or fallback values
        $fname = $request->first_name ?? $user->first_name;
        $lname = $request->last_name ?? $user->last_name;
        $email = $request->email ?? $user->email;
        $phone = $request->phone ?? $user->phone;

        // Validate phone format (Malaysian number: starts with 01, 10–11 digits total)
        $validator = Validator::make(['phone' => $phone], [
            'phone' => ['required', 'regex:/^01[0-9]{8,9}$/']
        ]);

        if ($validator->fails()) {
            // Determine where the phone number came from
            $source = $request->has('phone') ? 'request' : 'profile';

            if ($source === 'profile') {
                Session::flash('warning', __('Your profile phone number is invalid. Please update it to a valid Malaysian phone number in your profile settings') . '.');
            } else {
                Session::flash('warning', __('Invalid phone number format. Please enter a valid Malaysian phone number') . '.');
            }

            return redirect()->back()->withInput();
        }

        // Save the original request
        Session::put('request', $request->all());
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Booking End ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $paymentMethod = PaymentGateway::where('keyword', 'toyyibpay')->first();
        $paydata = $paymentMethod->convertAutoData();

        $ref = uniqid();
        session()->put('toyyibpay_ref_id', $ref);
        $bill_description = 'Package Purchase via toyyibpay';

        $some_data = array(
            'userSecretKey' => $paydata['secret_key'],
            'categoryCode' => $paydata['category_code'],
            'billName' => 'Package Purchase',
            'billDescription' => $bill_description,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $_amount * 100,
            'billReturnUrl' => $_success_url,
            'billExternalReferenceNo' => $ref,
            'billTo' => $fname . ' ' . $lname,
            'billEmail' => $email,
            'billPhone' => $phone,
        );


        if ($paydata['sandbox_status'] == 1) {
            $host = 'https://dev.toyyibpay.com/'; // for development environment
        } else {
            $host = 'https://toyyibpay.com/'; // for production environment
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $host . 'index.php/api/createBill');  // sandbox will be dev.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $response = json_decode($result, true);

        if (!empty($response[0])) {
            return redirect($host . $response[0]["BillCode"]);
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

        $ref = session()->get('toyyibpay_ref_id');
        if ($request['status_id'] == 1 && $request['order_id'] == $ref) {
            $paymentFor = Session::get('paymentFor');
            $package = Package::find($requestData['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = json_encode($request->all());
            $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
            $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
            $requestData['currency'] = $be->base_currency_text ?? "USD";
            $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
            $requestData['package_price'] = $package->price;
            $requestData['payment_method'] = 'Mollie';
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
            return redirect()->route('membership.perfect_money.cancel');
        }
    }
}
