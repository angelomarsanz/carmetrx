<?php

namespace App\Http\Controllers\Front;

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
use App\Http\Helpers\MegaMailer;
use App\Http\Helpers\UserPermissionHelper;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Models\Coupon;
use App\Models\Language;
use App\Models\Membership;
use App\Models\OfflineGateway;
use App\Models\Package;
use App\Models\User;
use App\Services\Tenant\RegistrationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
         
        $coupon = Coupon::where('code', Session::get('coupon'))->first();
        if (!empty($coupon)) {
            $coupon_count = $coupon->total_uses;
            if ($coupon->maximum_uses_limit != 999999) {
                if ($coupon_count == $coupon->maximum_uses_limit) {
                    Session::forget('coupon');
                    session()->flash('warning', __('This coupon reached maximum limit'));
                    return redirect()->back();
                }
            }
        }

        $offline_payment_gateways = OfflineGateway::all()->pluck('name')->toArray();
        $currentLang = session()->has('lang') ?
            (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $request['status'] = 1;
        $request['mode'] = 'online';
        $request['receipt_name'] = null;
        Session::put('paymentFor', 'membership');
        $title = "You are purchasing a membership";
        $description = "Congratulation you are going to join our membership.Please make a payment for confirming your membership now!";
        if ($request->package_type == "trial") {
            $package = Package::find($request['package_id']);
            $request['price'] = 0.00;
            $request['payment_method'] = "-";
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = "Trial";
            $user = $this->store($request->all(), $transaction_id, $transaction_details, $request->price, $be, $request->password);

            $lastMemb = $user->memberships()->orderBy('id', 'DESC')->first();
            $activation = Carbon::parse($lastMemb->start_date);
            $expire = Carbon::parse($lastMemb->expire_date);

            $tenantRegistration = new RegistrationService();

            $file_name = $tenantRegistration->makeInvoice($user, $lastMemb, $package->title, $bs->website_title);

            $tenantRegistration->storeFeatures($user->id, $package->id);
            $tenantRegistration->initializeDefaults($user);

            $mailer = new MegaMailer();
            $data = [
                'toMail' => $user->email,
                'toName' => $user->fname,
                'username' => $user->username,
                'package_title' => $package->title,
                'package_price' => ($be->base_currency_text_position == 'left' ? $be->base_currency_text . ' ' : '') . $package->price . ($be->base_currency_text_position == 'right' ? ' ' . $be->base_currency_text : ''),
                'activation_date' => $activation->toFormattedDateString(),
                'expire_date' => Carbon::parse($expire->toFormattedDateString())->format('Y') == '9999' ? 'Lifetime' : $expire->toFormattedDateString(),
                'membership_invoice' => $file_name,
                'website_title' => $bs->website_title,
                'templateType' => 'registration_with_trial_package',
                'type' => 'registrationWithTrialPackage'
            ];
            $mailer->mailFromAdmin($data);
            session()->flash('success', __('successful_payment'));
            return redirect()->route('membership.trial.success');
        } elseif ($request->price == 0) {
            $package = Package::find($request['package_id']);
            $request['price'] = 0.00;
            $request['payment_method'] = "-";
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $transaction_details = "Free";
            $user = $this->store($request->all(), $transaction_id, $transaction_details, $request->price, $be, $request->password);

            $lastMemb = $user->memberships()->orderBy('id', 'DESC')->first();
            $activation = Carbon::parse($lastMemb->start_date);
            $expire = Carbon::parse($lastMemb->expire_date);
            
            $tenantRegistration = new RegistrationService();

            $file_name = $tenantRegistration->makeInvoice($user, $lastMemb, $package->title, $bs->website_title);

            $tenantRegistration->storeFeatures($user->id, $package->id);
            $tenantRegistration->initializeDefaults($user);

            if (Session::has('coupon_amount')) {
                $mailTemp = 'registration_with_premium_package';
                $mailType = 'registrationWithPremiumPackage';
            } else {
                $mailTemp = 'registration_with_free_package';
                $mailType = 'registrationWithFreePackage';
            }

            $mailer = new MegaMailer();
            $data = [
                'toMail' => $user->email,
                'toName' => $user->fname,
                'username' => $user->username,
                'package_title' => $package->title,
                'package_price' => ($be->base_currency_text_position == 'left' ? $be->base_currency_text . ' ' : '') . $package->price . ($be->base_currency_text_position == 'right' ? ' ' . $be->base_currency_text : ''),
                'activation_date' => $activation->toFormattedDateString(),
                'expire_date' => Carbon::parse($expire->toFormattedDateString())->format('Y') == '9999' ? 'Lifetime' : $expire->toFormattedDateString(),
                'membership_invoice' => $file_name,
                'website_title' => $bs->website_title,
                'templateType' => $mailTemp,
                'type' => $mailType
            ];

            if (Session::has('coupon_amount')) {
                $data['discount'] = ($be->base_currency_text_position == 'left' ? $be->base_currency_text . ' ' : '') . $lastMemb->discount . ($be->base_currency_text_position == 'right' ? ' ' . $be->base_currency_text : '');
                $data['total'] = ($be->base_currency_text_position == 'left' ? $be->base_currency_text . ' ' : '') . $lastMemb->price . ($be->base_currency_text_position == 'right' ? ' ' . $be->base_currency_text : '');
            }

            $mailer->mailFromAdmin($data);
            session()->flash('success', __('successful_payment'));
            return redirect()->route('success.page');
        } elseif ($request->payment_method == "Paypal") {

            // $amount = round(($request->price / $be->base_currency_rate), 2);
            $paypal = new PaypalController();
            $cancel_url = route('membership.paypal.cancel');
            $success_url = route('membership.paypal.success');
            return $paypal->paymentProcess($request, $request->price, $title, $success_url, $cancel_url, $be);
        } elseif ($request->payment_method == "Stripe") {

            $amount = round(($request->price / $be->base_currency_rate), 2);
            $stripe = new StripeController();
            $cancel_url = route('membership.stripe.cancel');
            return $stripe->paymentProcess($request, $amount, $title, NULL, $cancel_url, $be->base_currency_text);
        } elseif ($request->payment_method == "Paytm") {

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
            $amount = $request->price;
            $email = $request->email;
            $success_url = route('membership.mercadopago.success');
            $cancel_url = route('membership.mercadopago.cancel');
            $mercadopagoPayment = new MercadopagoController();
            return $mercadopagoPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $be->base_currency_text);
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
        }
         elseif ($request->payment_method == 'Yoco') {
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
            DB::beginTransaction();

            try {
                $package = Package::findOrFail($request['package_id']);
                $request['mode'] = 'offline';
                $request['status'] = 0;
                $request['receipt_name'] = null;

                if ($request->hasFile('receipt')) {
                    $filename = time() . '.' . $request->file('receipt')->getClientOriginalExtension();
                    $directory = public_path("assets/front/img/membership/receipt/");
                    if (!file_exists($directory)) mkdir($directory, 0775, true);
                    $request->file('receipt')->move($directory, $filename);
                    $request['receipt_name'] = $filename;
                }

                $amount = round(($request->price / $be->base_currency_rate), 2);
                $transaction_id = UserPermissionHelper::uniqidReal(8);
                $transaction_details = "offline";
                $password = $request->password;

                // Store user and membership info
                $user = $this->store($request, $transaction_id, json_encode($transaction_details), $amount, $be, $password);

                $lastMemb = $user->memberships()->orderBy('id', 'DESC')->first();
                $activation = Carbon::parse($lastMemb->start_date);
                $expire = Carbon::parse($lastMemb->expire_date);

                $tenantRegistration = new RegistrationService();
                $websiteTitle = $be->website_title ?? '';

                $file_name = $tenantRegistration->makeInvoice($user, $lastMemb, $package->title, $websiteTitle);

                $tenantRegistration->storeFeatures($user->id, $package->id);
                $tenantRegistration->initializeDefaults($user);

                DB::commit();

                session()->flash('success', __('successful_payment'));
                return redirect()->route('membership.offline.success');
            } catch (\Exception $e) {

                DB::rollBack();
                if (!empty($filename) && file_exists(public_path("assets/front/img/membership/receipt/" . $filename))) {
                    unlink(public_path("assets/front/img/membership/receipt/" . $filename));
                }
                return redirect()->back()->with('error', __('Something went wrong during offline registration. Please try again later') . '.')->withInput($request->all());
            }
        }
    }

    public function store($data, $transaction_id, $transaction_details, $price, $be, $password)
    {
        $Tenant = new User();
        $tenant =  $Tenant->register($data);
        $tenantId = $tenant->id;

        Membership::create([
            'user_id' => $tenantId,
            'package_price' => $price,
            'discount' => $data['discount'] ?? 0,
            'coupon_code' => $data['coupon_code'] ?? null,
            'price' => $data['price'],
            'currency' => $be['base_currency_symbol'],
            'currency_symbol' => $be['base_currency_symbol'],
            'payment_method' => $data["payment_method"],
            'transaction_id' => $transaction_id,
            'status' => $data["status"],
            'is_trial' =>  $data["is_trial"] ?? 0,
            'trial_days' => $data["trial_days"] ?? 0,
            'receipt' => $data["receipt_name"] ?? null,
            'transaction_details' => $transaction_details ?? null,
            'settings' => $data['settings'] ?? null,
            'package_id' => $data['package_id'],
            'start_date' => $data['start_date'],
            'expire_date' => $data['expire_date'],
        ]);

        return $tenant;
    }

    public function onlineSuccess()
    {
        Session::forget('coupon');
        Session::forget('coupon_amount');
        return view('front.success');
    }
    public function paymentInstruction(Request $request)
    {
        $offline = OfflineGateway::query()
            ->where('name', $request->name)
            ->select('short_description', 'instructions', 'is_receipt')
            ->first();

        return response()->json([
            'description' => $offline->short_description,
            'instructions' => $offline->instructions,
            'is_receipt' => $offline->is_receipt
        ]);
    }

    public function offlineSuccess()
    {
        Session::forget('coupon');
        Session::forget('coupon_amount');
        return view('front.offline-success');
    }

    public function trialSuccess()
    {
        Session::forget('coupon');
        Session::forget('coupon_amount');
        return view('front.trial-success');
    }

    public function paymentCancel()
    {
        $requestData = Session::get('request');
        $paymentFor = Session::get('paymentFor');
        session()->flash('warning', "Payment Canceled");
        if ($paymentFor == "membership") {
            return redirect()->route('front.register.view', ['status' => $requestData['package_type'], 'id' => $requestData['package_id']])->withInput($requestData);
        } else {
            return redirect()->route('user.plan.extend.checkout', ['package_id' => $requestData['package_id']])->withInput($requestData);
        }
    }

    public function coupon(Request $request)
    {
        if (session()->has('coupon')) {
            return __('Coupon already applied');
        }
        $coupon = Coupon::where('code', $request->coupon)->first();
        if (empty($coupon)) {
            return __('This coupon does not exist');
        }

        $coupon_count = $coupon->total_uses;
        if ($coupon->maximum_uses_limit != 999999) {
            if ($coupon_count >= $coupon->maximum_uses_limit) {
                return __('This coupon reached maximum limit');
            }
        }
        $start = Carbon::parse($coupon->start_date);
        $end = Carbon::parse($coupon->end_date);
        $today = Carbon::parse(Carbon::now()->format('m/d/Y'));
        $packages = $coupon->packages;
        $packages = json_decode($packages, true);
        $packages = !empty($packages) ? $packages : [];
        if (!in_array($request->package_id, $packages)) {
            return __('This coupon is not valid for this package');
        }

        if ($today->greaterThanOrEqualTo($start) && $today->lessThanOrEqualTo($end)) {
            $package = Package::find($request->package_id);
            $price = $package->price;
            if ($coupon->type == 'percentage') {
                $cAmount = ($price * $coupon->value) / 100;
            } else {
                $cAmount = $coupon->value;
            }

            Session::put('coupon', $request->coupon);
            Session::put('coupon_amount', round($cAmount, 2));
            return "success";
        } else {
            return __('This coupon does not exist');
        }
    }
}
