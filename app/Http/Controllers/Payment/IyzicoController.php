<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Traits\FrontendLanguage;
use App\Models\Package;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\PaymentGateway;
use App\Services\Tenant\RegistrationService;
use App\Services\Tenant\ExtendPackage;
use Illuminate\Support\Facades\Auth;

class IyzicoController extends Controller
{
    use FrontendLanguage;
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url, $_title, $bex)
    {
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Buy Plan Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        Session::put('request', $request->all());

        // dd($request->all());

        $currentLang = $this->defaultLang();
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Booking End ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $paymentMethod = PaymentGateway::where('keyword', 'iyzico')->first();
        $paydata = $paymentMethod->convertAutoData();
        //booking information end

        $paymentFor = Session::get('paymentFor');
        if ($paymentFor == 'membership') {
            $first_name = $request['first_name'];
            $last_name = $request['last_name'];
            $email = $request['email'];
            $address = $request['address'];
            $city = $request['city'];
            $country = $request['country'];
        } else {
            $profile_status =  $this->check_profile();
            if ($profile_status == 'incomplete') {
                Session::flash('warning', __('Please, Complete your profile before purchase using iyzico payment method') . '.');
                return redirect()->route('user-profile');
            }

            $first_name = Auth::guard('web')->user()->first_name;
            $last_name = Auth::guard('web')->user()->last_name;
            $email = Auth::guard('web')->user()->email;
            $address = Auth::guard('web')->user()->address;
            $city = Auth::guard('web')->user()->city;
            $country = Auth::guard('web')->user()->country;
        }
        $zip_code = $request['zip_code'];
        $identity_number = $request['identity_number'];
        $basket_id = 'B' . uniqid(999, 99999);
        $phone = $request->phone;

        $options = new \Iyzipay\Options();
        $options->setApiKey($paydata['api_key']);
        $options->setSecretKey($paydata['secret_key']);
        if ($paydata['sandbox_status'] == 1) {
            $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        } else {
            $options->setBaseUrl("https://api.iyzipay.com"); // production mode
        }


        $conversion_id = uniqid(9999, 999999);
        # create request class
        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::EN);
        $request->setConversationId($conversion_id);
        $request->setPrice($_amount);
        $request->setPaidPrice($_amount);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($basket_id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($_success_url);
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(uniqid());
        $buyer->setName($first_name);
        $buyer->setSurname($last_name);
        $buyer->setGsmNumber($phone);
        $buyer->setEmail($email);
        $buyer->setIdentityNumber($identity_number);
        $buyer->setLastLoginDate("");
        $buyer->setRegistrationDate("");
        $buyer->setRegistrationAddress($address);
        $buyer->setIp("");
        $buyer->setCity($city);
        $buyer->setCountry($country);
        $buyer->setZipCode($zip_code);
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($first_name);
        $shippingAddress->setCity($city);
        $shippingAddress->setCountry($country);
        $shippingAddress->setAddress($address);
        $shippingAddress->setZipCode($zip_code);
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($first_name);
        $billingAddress->setCity($city);
        $billingAddress->setCountry($country);
        $billingAddress->setAddress($address);
        $billingAddress->setZipCode($zip_code);
        $request->setBillingAddress($billingAddress);

        $q_id = uniqid(999, 99999);
        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId($q_id);
        $firstBasketItem->setName("Purchase Id " . $q_id);
        $firstBasketItem->setCategory1("Purchase or Booking");
        $firstBasketItem->setCategory2("");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice($_amount);
        $basketItems[0] = $firstBasketItem;

        $request->setBasketItems($basketItems);

        # make request
        $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request, $options);

        $paymentResponse = (array)$payWithIyzicoInitialize;


        foreach ($paymentResponse as $key => $data) {
            $paymentInfo = json_decode($data, true);
            if ($paymentInfo['status'] == 'success') {
                if (!empty($paymentInfo['payWithIyzicoPageUrl'])) {
                    Session::put('conversation_id', $conversion_id);
                    return redirect($paymentInfo['payWithIyzicoPageUrl']);
                }
            }
            return redirect($_cancel_url);
        }
    }

    // return to success page
    public function successPayment(Request $request)
    {
        $requestData = Session::get('request');

        $requestData['status'] = 0;
        $requestData['conversation_id'] = Session::get('conversation_id');

        $currentLang = $this->defaultLang();
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        /** Get the payment ID before session clear **/

        $paymentFor = Session::get('paymentFor');
        $package = Package::find($requestData['package_id']);
        $transaction_id = UserPermissionHelper::uniqidReal(8);
        $transaction_details = json_encode($request->all());

        $requestData['discount'] = session()->has('coupon_amount') ? session()->get('coupon_amount') : 0;
        $requestData['coupon_code'] = session()->has('coupon') ? session()->get('coupon') : NULL;
        $requestData['currency'] = $be->base_currency_text ?? "USD";
        $requestData['currency_symbol'] =  $be->base_currency_symbol ?? $be->base_currency_text;
        $requestData['package_price'] = $package->price;
        $requestData['payment_method'] = 'Iyzico';
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

    private function check_profile()
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            if (empty($user->first_name) || empty($user->address) || empty($user->city) || empty($user->country)) {
                return 'incomplete';
            } else {
                return 'completed';
            }
        } else {
            return 'incomplete';
        }
    }
}
