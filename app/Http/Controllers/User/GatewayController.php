<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\OfflineGateway;
use App\Models\User\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GatewayController extends Controller
{


    public function index()
    {
        $data['paypal'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'paypal')->first();
        $data['stripe'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'stripe')->first();
        $data['paystack'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'paystack')->first();
        $data['paytm'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'paytm')->first();
        $data['flutterwave'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'flutterwave')->first();
        $data['instamojo'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'instamojo')->first();
        $data['mollie'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'mollie')->first();
        $data['razorpay'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'razorpay')->first();
        $data['mercadopago'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'mercadopago')->first();
        $data['anet'] = PaymentGateway::query()->where('user_id', Auth::guard('web')->user()->id)->where('keyword', 'authorize.net')->first();

        return view('user.gateways.index', $data);
    }

    public function paypalUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'paypal'
            ],
            $request->except(['_token']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'paypal',
                'name' => 'PayPal',
                'type' => 'automatic',
                'information' => json_encode([
                    'client_id' => $request->client_id,
                    'sandbox_check' => $request->sandbox_check,
                    'client_secret' => $request->client_secret,
                    'text' => "Pay via your PayPal accounts."
                ]),
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function stripeUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'stripe'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'stripe',
                'name' => 'Stripe',
                'type' => 'automatic',
                'information' => json_encode([
                    'key' => $request->key,
                    'secret' => $request->secret,
                    'text' => "Pay via your Credit account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function paystackUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'paystack'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'paystack',
                'name' => 'Paystack',
                'type' => 'automatic',
                'information' => json_encode([
                    'key' => $request->key,
                    'email' => $request->email,
                    'text' => "Pay via your Paystack account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function paytmUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'paytm'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'paytm',
                'name' => 'Paytm',
                'type' => 'automatic',
                'information' => json_encode([
                    'environment' => $request->environment,
                    'merchant' => $request->merchant,
                    'secret' => $request->secret,
                    'website' => $request->website,
                    'industry' => $request->industry,
                    'text' => "Pay via your Paytm account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function flutterwaveUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'flutterwave'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'flutterwave',
                'name' => 'Flutterwave',
                'type' => 'automatic',
                'information' => json_encode([
                    'public_key' => $request->public_key,
                    'secret_key' => $request->secret_key,
                    'text' => "Pay via your Flutterwave account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function instamojoUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'instamojo'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'instamojo',
                'name' => 'Instamojo',
                'type' => 'automatic',
                'information' => json_encode([
                    'key' => $request->key,
                    'token' => $request->token,
                    'sandbox_check' => $request->sandbox_check,
                    'text' => "Pay via your Instamojo account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function mollieUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'mollie'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'mollie',
                'name' => 'Mollie',
                'type' => 'automatic',
                'information' => json_encode([
                    'key' => $request->key,
                    'text' => "Pay via your Mollie Payment account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function razorpayUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'razorpay'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'razorpay',
                'name' => 'Razorpay',
                'type' => 'automatic',
                'information' => json_encode([
                    'key' => $request->key,
                    'secret' => $request->secret,
                    'text' => "Pay via your Razorpay account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function anetUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'authorize.net'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'authorize.net',
                'name' => 'Authorize.net',
                'type' => 'automatic',
                'information' => json_encode([
                    'login_id' => $request->login_id,
                    'transaction_key' => $request->transaction_key,
                    'public_key' => $request->public_key,
                    'sandbox_check' => $request->sandbox_check,
                    'text' => "Pay via your Authorize.net account."
                ])
            ]
        );

        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function mercadopagoUpdate(Request $request)
    {
        PaymentGateway::query()->updateOrCreate(
            [
                'user_id' => Auth::guard('web')->user()->id,
                'keyword' => 'mercadopago'
            ],
            $request->except(['_token', 'information', 'keyword']) + [
                'user_id' => Auth::guard('web')->user()->id,
                'status' => (int)$request->status,
                'keyword' => 'mercadopago',
                'name' => 'Mercado Pago',
                'type' => 'automatic',
                'information' => json_encode([
                    'token' => $request->token,
                    'sandbox_check' => $request->sandbox_check,
                    'text' => "Pay via your Mercado Pago account."
                ])
            ]
        );
        session()->flash('success', __("Updated successfully!"));
        return back();
    }

    public function offline(Request $request)
    {
        $data['ogateways'] = OfflineGateway::query()
            ->where('user_id', Auth::guard('web')->user()->id)
            ->orderBy('id', 'DESC')
            ->get();
        return view('user.gateways.offline.index', $data);
    }

    public function store(Request $request)
    {

        $rules = [
            'name' => 'required|max:100',
            'short_description' => 'nullable',
            'serial_number' => 'required|integer',
            'is_receipt' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        OfflineGateway::create($request->except('user_id',  'instructions') + [
            'user_id' => Auth::guard('web')->user()->id,
            'instructions' => clean($request->instructions)
        ]);

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function update(Request $request)
    {

        $rules = [
            'name' => 'required|max:100',
            'short_description' => 'nullable',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->except('_token', 'ogateway_id', 'instructions');
        $in['instructions'] = clean($request->instructions);

        OfflineGateway::query()
            ->where('user_id', Auth::guard('web')->user()->id)
            ->where('id', $request->ogateway_id)
            ->update($in);

        Session::flash('success', __("Updated successfully!"));
        return "success";
    }

    public function status(Request $request)
    {
        OfflineGateway::query()
            ->where('user_id', Auth::guard('web')->user()->id)
            ->find($request->ogateway_id)
            ->update(['status' => $request->status]);
        Session::flash('success', __("Updated successfully!"));
        return back();
    }

    public function delete(Request $request)
    {
        OfflineGateway::query()
            ->where('user_id', Auth::guard('web')->user()->id)
            ->findOrFail($request->ogateway_id)
            ->delete();
        Session::flash('success', __('Deleted successfully!'));
        return back();
    }
}
