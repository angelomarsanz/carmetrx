<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\AuthorizenetController;
use App\Http\Controllers\Payment\FlutterWaveController;
use App\Http\Controllers\Payment\InstamojoController;
use App\Http\Controllers\Payment\IyzicoController;
use App\Http\Controllers\Payment\MercadopagoController;
use App\Http\Controllers\Payment\MidtransController;
use App\Http\Controllers\Payment\MollieController;
use App\Http\Controllers\Payment\MyFatoorahController;
use App\Http\Controllers\Payment\PaypalController;
use App\Http\Controllers\Payment\PaystackController;
use App\Http\Controllers\Payment\PaytabsController;
use App\Http\Controllers\Payment\PaytmController;
use App\Http\Controllers\Payment\PerfectMoneyController;
use App\Http\Controllers\Payment\PhonePeController;
use App\Http\Controllers\Payment\RazorpayController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\Payment\ToyyibpayController;
use App\Http\Controllers\Payment\XenditController;
use App\Http\Controllers\Payment\YocoController;
use App\Http\Helpers\UserPermissionHelper;
use App\Http\Requests\Checkout\ExtendRequest;
use App\Models\Language;
use App\Models\Membership;
use App\Models\OfflineGateway;
use App\Models\Package;
use App\Models\User;
use App\Models\User\UserPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserCheckoutController extends Controller
{
    public function checkout(ExtendRequest $request)
    {
        $offline_payment_gateways = OfflineGateway::all()->pluck('name')->toArray();
        $currentLang = session()->has('lang') ?
            (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $request['status'] = "1";
        $request['receipt_name'] = null;
        $request['email'] = Auth::guard('web')->user()->email;
        Session::put('paymentFor', 'extend');
        $title = "You are extending your membership";
        $description = "Congratulation you are going to join our membership.Please make a payment for confirming your membership now!";
        if ($request->price == 0) {
            $request['price'] = 0.00;
            $request['payment_method'] = "-";
            $transaction_details = "Free";
            $password = uniqid('qrcode');
            $package = Package::find($request['package_id']);
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $user = $this->store($request->all(), $transaction_id, $transaction_details, $request['price'], $be, $password);
            $subject = "You made your membership purchase successful";
            $body = "You made a payment. This is a confirmation mail from us. Please see the invoice attachment below";

            $lastMemb = $user->memberships()->orderBy('id', 'DESC')->first();


            $file_name = $this->makeInvoice($request->all(), "extend", $user, $password, $request['price'], $request["payment_method"], $user->phone_number, $be->base_currency_symbol_position, $be->base_currency_symbol, $be->base_currency_text, $transaction_id, $package->title, $lastMemb);
            $this->sendMailWithPhpMailer($request->all(), $file_name, $be, $subject, $body, $user->email, $user->first_name . ' ' . $user->last_name);
            Session::forget('request');
            Session::forget('paymentFor');
            return redirect()->route('success.page');
        } elseif ($request->payment_method == "Paypal") {
            $amount = round(($request->price / $be->base_currency_rate), 2);
            $paypal = new PaypalController;
            $cancel_url = route('membership.paypal.cancel');
            $success_url = route('membership.paypal.success');
            return $paypal->paymentProcess($request, $amount, $title, $success_url, $cancel_url, $be);
        } elseif ($request->payment_method == "Stripe") {

            $amount = round(($request->price / $be->base_currency_rate), 2);
            $stripe = new StripeController();
            $cancel_url = route('membership.stripe.cancel');
            return $stripe->paymentProcess($request, $amount, $title, NULL, $cancel_url, $be->base_currency_text);
        } elseif ($request->payment_method == "Paytm") {
            if ($be->base_currency_text != "INR") {
                session()->flash('warning', __('only_paytm_INR'));
                return back()->withInput($request->all());
            }
            $amount = $request->price;
            $item_number = uniqid('paytm-') . time();
            $callback_url = route('membership.paytm.status');
            $paytm = new PaytmController();
            return $paytm->paymentProcess($request, $amount, $item_number, $callback_url, $be->base_currency_text);
        } elseif ($request->payment_method == "Paystack") {

            $amount = $request->price;
            $email = $request->email;
            $success_url = route('membership.paystack.success');
            $cancle_url = route('membership.paystack.cancle');
            $payStack = new PaystackController();
            return $payStack->paymentProcess($request, $amount, $email, $success_url, $cancle_url, $be->base_currency_text);
        } elseif ($request->payment_method == "Razorpay") {

            $amount = $request->price;
            $item_number = uniqid('razorpay-') . time();
            $cancel_url = route('membership.razorpay.cancel');
            $success_url = route('membership.razorpay.success');
            $razorpay = new RazorpayController();
            return $razorpay->paymentProcess($request, $amount, $item_number, $success_url, $cancel_url,  $title, $description, $bs, $be->base_currency_text);
        } elseif ($request->payment_method == "Instamojo") {

            $amount = $request->price;
            $success_url = route('membership.instamojo.success');
            $cancel_url = route('membership.instamojo.cancel');
            $instaMojo = new InstamojoController();
            return $instaMojo->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be->base_currency_text);
        } elseif ($request->payment_method == "Mercado Pago") {
            if ($be->base_currency_text != "BRL") {
                session()->flash('warning', __('only_mercadopago_BRL'));
                return back()->withInput($request->all());
            }
            $amount = $request->price;
            $email = $request->email;
            $success_url = route('membership.mercadopago.success');
            $cancel_url = route('membership.mercadopago.cancel');
            $mercadopagoPayment = new MercadopagoController();
            return $mercadopagoPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $be);
        } elseif ($request->payment_method == "Flutterwave") {


            $amount = round(($request->price / $be->base_currency_rate), 2);
            $email = $request->email;
            $item_number = uniqid('flutterwave-') . time();
            $cancel_url = route('membership.flutterwave.cancel');
            $success_url = route('membership.flutterwave.success');
            $flutterWave = new FlutterWaveController();
            return $flutterWave->paymentProcess($request, $amount, $email, $item_number, $success_url, $cancel_url, $be->base_currency_text);
        } elseif ($request->payment_method == "Authorize.net") {
       
            $amount = $request->price;
            $cancel_url = route('membership.anet.cancel');
            $anetPayment = new AuthorizenetController();
            return $anetPayment->paymentProcess($request, $amount, $cancel_url, $title, $be->base_currency_text);
        } elseif ($request->payment_method == "Mollie Payment") {
        
            $amount = round(($request->price / $be->base_currency_rate), 2);
            $success_url = route('membership.mollie.success');
            $cancel_url = route('membership.mollie.cancel');
            $molliePayment = new MollieController();
            return $molliePayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be->base_currency_text);
        } elseif ($request->payment_method == "PhonePe") {
            if ($be->base_currency_text != 'INR') {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }
            $amount = $request->price;
            $success_url = route('membership.phonepe.success');
            $cancel_url = route('membership.phonepe.cancel');
            $phonepePayment = new PhonePeController();
            return $phonepePayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be);
        } elseif ($request->payment_method == 'Yoco') {
            // changing the currency before redirect to Stripe
            if ($be->base_currency_text != 'ZAR') {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }

            $amount = $request->price;
            $cancel_url = route('membership.perfect_money.cancel');
            $success_url = route('membership.yoco.success');

            $yoco = new YocoController();
            return $yoco->paymentProcess($request, $amount, $title, $success_url, $cancel_url);
        } elseif ($request->payment_method == 'Xendit') {
            // changing the currency before redirect to xendit

            $allowed_currency = array('IDR', 'PHP', 'USD', 'SGD', 'MYR');
            if (!in_array($be->base_currency_text, $allowed_currency)) {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }

            $amount = $request->price;
            $xendit = new XenditController();
            $success_url = route('membership.xendit.success');
            $cancel_url = route('membership.payment.cancel');
            return $xendit->paymentProcess($request, $amount, $title, $success_url, $cancel_url, $be);
        } elseif ($request->payment_method == "Perfect Money") {
            if ($be->base_currency_text != 'USD') {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }
            $amount = $request->price;
            $success_url = route('membership.perfect_money.success');
            $cancel_url = route('membership.perfect_money.cancel');
            $perfectMoneyPayment = new PerfectMoneyController();
            return $perfectMoneyPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be);
        } elseif ($request->payment_method == "MyFatoorah") {

            $allowed_currency = array('KWD', 'SAR', 'BHD', 'AED', 'QAR', 'OMR', 'JOD');
            if (!in_array($be->base_currency_text, $allowed_currency)) {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }
            $amount = $request->price;
            $success_url = null;
            $cancel_url = route('myfatoorah.cancel');
            $myfatoorahPayment = new MyFatoorahController();
            return $myfatoorahPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be);
        } elseif ($request->payment_method == "ToyyibPay") {
            if ($be->base_currency_text != 'RM') {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }
            $amount = $request->price;
            $success_url = route('membership.toyyibpay.success');
            $cancel_url = route('membership.perfect_money.cancel');
            $yocoPayment = new ToyyibpayController();
            return $yocoPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be);
        } elseif ($request->payment_method == "PayTabs") {
            $paytabInfo = paytabInfo('admin', null);

            // changing the currency before redirect to Stripe
            if ($be->base_currency_text != $paytabInfo['currency']) {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }
            $amount = $request->price;
            $success_url = route('membership.paytabs.success');
            $cancel_url = route('membership.perfect_money.cancel');
            $paytabPayment = new PaytabsController();
            return $paytabPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be);
        } elseif ($request->payment_method == "Iyzico") {
            if ($be->base_currency_text != 'TRY') {
                return redirect()->back()->with('error', __('invalid_currency'))->withInput($request->all());
            }
            $amount = $request->price;
            $success_url = route('membership.iyzico.success');
            $cancel_url = route('membership.perfect_money.cancel');
            $iyzicoPayment = new IyzicoController();
            return $iyzicoPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $be);
        } elseif ($request->payment_method == 'Midtrans') {

            if ($be->base_currency_text != "IDR") {
                session()->flash('warning', __('invalid_currency'));
                return back()->withInput($request->all());
            }

            $amount = $request->price;
            $email = $request->email;
            $cancel_url = route('membership.midtrans.payment.cancel');
            $success_url = route('membership.midtrans.success');
            $midtruns = new MidtransController();
            return $midtruns->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif (in_array($request->payment_method, $offline_payment_gateways)) {
            $request['status'] = "0";
            if ($request->hasFile('receipt')) {
                $filename = time() . '.' . $request->file('receipt')->getClientOriginalExtension();
                $directory = public_path('assets/front/img/membership/receipt');
                @mkdir($directory, 0775, true);
                $request->file('receipt')->move($directory, $filename);
                $request['receipt_name'] = $filename;
            }
            $amount = $request->price;
            $transaction_id = \App\Http\Helpers\UserPermissionHelper::uniqidReal(8);
            $transaction_details = "offline";
            $password = uniqid('qrcode');
            $this->store($request, $transaction_id, json_encode($transaction_details), $amount, $be, $password);
            return view('front.offline-success');
        }
    }

    public function store($request, $transaction_id, $transaction_details, $amount, $be, $password)
    {
        $user = User::query()->findOrFail($request['user_id']);
        $previousMembership = Membership::query()
            ->select('id', 'package_id', 'is_trial')
            ->where([
                ['user_id', $user->id],
                ['start_date', '<=', Carbon::now()->toDateString()],
                ['expire_date', '>=', Carbon::now()->toDateString()]
            ])
            ->where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->first();
        if (!is_null($previousMembership)) {
            $previousPackage = Package::query()
                ->select('term')
                ->where('id', $previousMembership->package_id)
                ->first();

            if (($previousPackage->term === 'lifetime' || $previousMembership->is_trial == 1) && $transaction_details != '"offline"') {
                $membership = Membership::find($previousMembership->id);
                $membership->expire_date = Carbon::parse($request['start_date']);
                $membership->save();
            }
        }
        if ($user) {
            Membership::create([
                'price' => $request['price'],
                'currency' => $be->base_currency_text,
                'currency_symbol' => $be->base_currency_symbol,
                'payment_method' => $request["payment_method"],
                'transaction_id' => $transaction_id,
                'status' => $request["status"],
                'receipt' => $request["receipt_name"],
                'transaction_details' => $transaction_details,
                'settings' => json_encode($be),
                'package_id' => $request['package_id'],
                'user_id' => $user->id,
                'start_date' => Carbon::parse($request['start_date']),
                'expire_date' => Carbon::parse($request['expire_date']),
                'is_trial' => 0,
                'trial_days' => 0,
            ]);
        }
        return $user;
    }
}
