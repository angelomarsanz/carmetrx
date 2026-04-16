<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\OfflineGateway;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GatewayController extends Controller
{
    public function index()
    {
        $data['paypal'] = PaymentGateway::where('keyword', 'paypal')->first();
        $data['stripe'] = PaymentGateway::where('keyword', 'stripe')->first();
        $data['paystack'] = PaymentGateway::where('keyword', 'paystack')->first();
        $data['paytm'] = PaymentGateway::where('keyword', 'paytm')->first();
        $data['flutterwave'] = PaymentGateway::where('keyword', 'flutterwave')->first();
        $data['instamojo'] = PaymentGateway::where('keyword', 'instamojo')->first();
        $data['mollie'] = PaymentGateway::where('keyword', 'mollie')->first();
        $data['razorpay'] = PaymentGateway::where('keyword', 'razorpay')->first();
        $data['mercadopago'] = PaymentGateway::where('keyword', 'mercadopago')->first();
        $data['anet'] = PaymentGateway::where('keyword', 'authorize.net')->first();

        $data['yoco'] = PaymentGateway::where('keyword', 'yoco')->first();
        $data['xendit'] = PaymentGateway::where('keyword', 'xendit')->first();
        $data['perfect_money'] = PaymentGateway::where('keyword', 'perfect_money')->first();
        $data['myfatoorah'] = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $data['iyzico'] = PaymentGateway::where('keyword', 'iyzico')->first();
        $data['paytabs'] = PaymentGateway::where('keyword', 'paytabs')->first();
        $data['toyyibpay'] = PaymentGateway::where('keyword', 'toyyibpay')->first();
        $data['midtrans'] = PaymentGateway::where('keyword', 'midtrans')->first();
        $data['phonepe'] = PaymentGateway::where('keyword', 'phonepe')->first();

        return view('admin.gateways.index', $data);
    }

    public function paypalUpdate(Request $request)
    {
        $paypal = PaymentGateway::find(15);
        $paypal->status = $request->status;

        $information = [];
        $information['client_id'] = $request->client_id;
        $information['client_secret'] = $request->client_secret;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your PayPal account.";

        $paypal->information = json_encode($information);

        $paypal->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function stripeUpdate(Request $request)
    {
        $stripe = PaymentGateway::find(14);
        $stripe->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret'] = $request->secret;
        $information['text'] = "Pay via your Credit account.";

        $stripe->information = json_encode($information);

        $stripe->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function paystackUpdate(Request $request)
    {
        $paystack = PaymentGateway::find(12);
        $paystack->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['email'] = $request->email;
        $information['text'] = "Pay via your Paystack account.";

        $paystack->information = json_encode($information);

        $paystack->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function paytmUpdate(Request $request)
    {
        $paytm = PaymentGateway::find(11);
        $paytm->status = $request->status;

        $information = [];
        $information['environment'] = $request->environment;
        $information['merchant'] = $request->merchant;
        $information['secret'] = $request->secret;
        $information['website'] = $request->website;
        $information['industry'] = $request->industry;
        $information['text'] = "Pay via your paytm account.";

        $paytm->information = json_encode($information);

        $paytm->save();


        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function flutterwaveUpdate(Request $request)
    {
        $flutterwave = PaymentGateway::find(6);
        $flutterwave->status = $request->status;

        $information = [];
        $information['public_key'] = $request->public_key;
        $information['secret_key'] = $request->secret_key;
        $information['text'] = "Pay via your Flutterwave account.";

        $flutterwave->information = json_encode($information);

        $flutterwave->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function instamojoUpdate(Request $request)
    {
        $instamojo = PaymentGateway::find(13);
        $instamojo->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['token'] = $request->token;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your Instamojo account.";

        $instamojo->information = json_encode($information);

        $instamojo->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function mollieUpdate(Request $request)
    {
        $mollie = PaymentGateway::find(17);
        $mollie->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['text'] = "Pay via your Mollie Payment account.";

        $mollie->information = json_encode($information);

        $mollie->save();

        $arr = ['MOLLIE_KEY' => $request->key];
        setEnvironmentValue($arr);
        \Artisan::call('config:clear');

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function razorpayUpdate(Request $request)
    {
        $razorpay = PaymentGateway::find(9);
        $razorpay->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret'] = $request->secret;
        $information['text'] = "Pay via your Razorpay account.";

        $razorpay->information = json_encode($information);

        $razorpay->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function anetUpdate(Request $request)
    {
        $anet = PaymentGateway::find(20);
        $anet->status = $request->status;

        $information = [];
        $information['login_id'] = $request->login_id;
        $information['transaction_key'] = $request->transaction_key;
        $information['public_key'] = $request->public_key;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your Authorize.net account.";

        $anet->information = json_encode($information);

        $anet->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function mercadopagoUpdate(Request $request)
    {
        $mercadopago = PaymentGateway::find(19);
        $mercadopago->status = $request->status;

        $information = [];
        $information['token'] = $request->token;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your Mercado Pago account.";

        $mercadopago->information = json_encode($information);

        $mercadopago->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }

    public function offline(Request $request)
    {
        $data['ogateways'] = OfflineGateway::orderBy('id', 'DESC')->get();

        return view('admin.gateways.offline.index', $data);
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
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->all();

        OfflineGateway::create($in);

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

        $in = $request->except('_token', 'ogateway_id');

        OfflineGateway::where('id', $request->ogateway_id)->update($in);

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function status(Request $request)
    {
        $og = OfflineGateway::find($request->ogateway_id);
      
        $og->status = $request->status;
        $og->save();

        Session::flash('success', __('Updated successfully!'));
        return back();
    }

    public function delete(Request $request)
    {
        $ogateway = OfflineGateway::findOrFail($request->ogateway_id);
        $ogateway->delete();

        Session::flash('success', __('Deleted successfully!'));
        return back();
    }

    public function updateYocoInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'secret_key' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $information = [
            'secret_key' => $request->secret_key,
        ];

        // Retrieve existing or create new PaymentGateway with keyword 'yoco'
        $data = PaymentGateway::firstOrNew(['keyword' => 'yoco']);

        // If new record, set default values explicitly
        if (! $data->exists) {
            $data->keyword = 'yoco';          
            $data->name = 'Yoco';             
            $data->status = 1;                
        }

        // Update status and information from request
        $data->status = $request->status ?? $data->status;
        $data->information = json_encode($information);

        $data->save();

        Session::flash('success', __("Updated successfully"));

        return redirect()->back();
    }


    public function updateXenditInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'secret_key' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $information = [
            'secret_key' => $request->secret_key,
        ];

        // Retrieve existing or create new PaymentGateway with keyword 'xendit'
        $data = PaymentGateway::firstOrNew(['keyword' => 'xendit']);

        // If new record, set default values explicitly
        if (! $data->exists) {
            $data->keyword = 'xendit';          
            $data->name = 'Xendit';            
            $data->status = 1;                 
        }

        // Update status and information from request
        $data->status = $request->status ?? $data->status;
        $data->information = json_encode($information);

        $data->save();

        // Update environment variable and clear config cache
        $array = [
            'XENDIT_SECRET_KEY' => $request->secret_key,
        ];

        setEnvironmentValue($array);
        Artisan::call('config:clear');

        Session::flash('success', __("Updated successfully!"));

        return redirect()->back();
    }


    public function updatePerfectMoneyInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'perfect_money_wallet_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        // Retrieve existing or create new with keyword set
        $perfect_money = PaymentGateway::firstOrNew(['keyword' => 'perfect_money']);

        // If new record, set default values including keyword explicitly
        if (! $perfect_money->exists) {
            $perfect_money->keyword = 'perfect_money';  
            $perfect_money->name = 'Perfect Money';     
            $perfect_money->status = 1;                  
        }

        // Update status and information from request
        $perfect_money->status = $request->status ?? $perfect_money->status;
        $perfect_money->information = json_encode([
            'perfect_money_wallet_id' => $request->perfect_money_wallet_id
        ]);

        $perfect_money->save();

        Session::flash('success', __("Updated successfully!"));

        return redirect()->back();
    }


    // public function updateMyFatoorahInfo(Request $request)
    // {

    //     $rules = [
    //         'status' => 'required',
    //         'sandbox_status' => 'required',
    //         'token' => 'required',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator->errors());
    //     }

    //     $information = [
    //         'token' => $request->token,
    //         'sandbox_status' => $request->sandbox_status,
    //     ];

    //     // Retrieve existing or create new PaymentGateway with keyword 'myfatoorah'
    //     $data = PaymentGateway::firstOrNew(['keyword' => 'myfatoorah']);

    //     // If new record, set default values explicitly
    //     if (! $data->exists) {
    //         $data->keyword = 'myfatoorah';    
    //         $data->name = 'MyFatoorah';            
    //         $data->status = 1;                    
    //     }

    //     // Update status and information from request
    //     $data->status = $request->status ?? $data->status;
    //     $data->information = json_encode($information);
    //     dd($data->information);

    //     $data->save();

    //     // Update environment variables and clear config cache
    //     $array = [
    //         'MYFATOORAH_TOKEN' => $request->token,
    //         'MYFATOORAH_CALLBACK_URL' => route('myfatoorah.success'),
    //         'MYFATOORAH_ERROR_URL' => route('myfatoorah.cancel'),
    //     ];

    //     setEnvironmentValue($array);
    //     Artisan::call('config:clear');

    //     Session::flash('success', __("Updated successfully!"));

    //     return redirect()->back();
    // }
    public function updateMyFatoorahInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'sandbox_status' => 'required',
            'token' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        // 1. Update the .env file FIRST (fail early if there's an issue)
        $envUpdates = [
            'MYFATOORAH_TOKEN' => $request->token,
            'MYFATOORAH_CALLBACK_URL' => route('myfatoorah.success'),
            'MYFATOORAH_ERROR_URL' => route('myfatoorah.cancel'),
        ];

        try {
            setEnvironmentValue($envUpdates);
            Artisan::call('config:clear');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Failed to update .env file. Check permissions.'
            ]);
        }

        // 2. Now update the database (using the same token)
        $information = [
            'token' => $request->token,  // Ensure this matches .env
            'sandbox_status' => $request->sandbox_status,
        ];

        $data = PaymentGateway::firstOrNew(['keyword' => 'myfatoorah']);

        if (!$data->exists) {
            $data->keyword = 'myfatoorah';
            $data->name = 'MyFatoorah';
            $data->status = 1;
        }

        $data->status = $request->status ?? $data->status;
        $data->information = json_encode($information);
        $data->save();

        Session::flash('success', __("Updated successfully!"));
        return redirect()->back();
    }

    public function updateIyzicoInfo(Request $request)
    {
        
        $rules = [
            'status' => 'required',
            'api_key' => 'required',
            'secret_key' => 'required',
            'sandbox_status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $information = [
            'api_key' => $request->api_key,
            'secret_key' => $request->secret_key,
            'sandbox_status' => $request->sandbox_status,
        ];

        // Retrieve existing or create new PaymentGateway with keyword 'iyzico'
        $iyzico = PaymentGateway::firstOrNew(['keyword' => 'iyzico']);

        // If new record, set default values explicitly
        if (! $iyzico->exists) {
            $iyzico->keyword = 'iyzico';       
            $iyzico->name = 'Iyzico';          
            $iyzico->status = 1;              
        }

        // Update status and information from request
        $iyzico->status = $request->status ?? $iyzico->status;
        $iyzico->information = json_encode($information);

        $iyzico->save();

        $request->session()->flash('success', __("Updated successfully!"));

        return back();
    }


    public function updatePaytabsInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'country' => 'required',
            'server_key' => 'required',
            'profile_id' => 'required',
            'api_endpoint' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $information = [
            'server_key' => $request->server_key,
            'profile_id' => $request->profile_id,
            'country' => $request->country,
            'api_endpoint' => $request->api_endpoint,
        ];

        // Retrieve existing or create new PaymentGateway with keyword 'paytabs'
        $data = PaymentGateway::firstOrNew(['keyword' => 'paytabs']);

        // If new record, set default values explicitly
        if (! $data->exists) {
            $data->keyword = 'paytabs';       
            $data->name = 'PayTabs';          
            $data->status = 1;                
        }

        // Update status and information from request
        $data->status = $request->status ?? $data->status;
        $data->information = json_encode($information);

        $data->save();

        Session::flash('success', __("Updated successfully!"));

        return redirect()->back();
    }


    public function updateToyyibpayInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'sandbox_status' => 'required',
            'secret_key' => 'required',
            'category_code' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $information = [
            'sandbox_status' => $request->sandbox_status,
            'secret_key' => $request->secret_key,
            'category_code' => $request->category_code,
        ];

        // Retrieve existing or create new PaymentGateway with keyword 'toyyibpay'
        $data = PaymentGateway::firstOrNew(['keyword' => 'toyyibpay']);

        // If new record, set default values explicitly
        if (! $data->exists) {
            $data->keyword = 'toyyibpay';    
            $data->name = 'ToyyibPay';      
            $data->status = 1;               
        }

        // Update status and information from request
        $data->status = $request->status ?? $data->status;
        $data->information = json_encode($information);

        $data->save();

        Session::flash('success', __("Updated successfully!"));

        return redirect()->back();
    }

    public function updateMidtransInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'server_key' => 'required',
            'is_production' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $information = [
            'server_key' => $request->server_key,
            'is_production' => $request->is_production,
        ];

        // Retrieve existing or create new PaymentGateway with keyword 'midtrans'
        $midtrans = PaymentGateway::firstOrNew(['keyword' => 'midtrans']);

        // If new record, set default values explicitly
        if (! $midtrans->exists) {
            $midtrans->keyword = 'midtrans';      
            $midtrans->name = 'Midtrans';        
            $midtrans->status = 1;                 
        }

        // Update status and information from request
        $midtrans->status = $request->status ?? $midtrans->status;
        $midtrans->information = json_encode($information);

        $midtrans->save();

        Session::flash('success', __("Updated successfully!"));

        return back();
    }


    public function updatePhonepeInfo(Request $request)
    {
        $rules = [
            'status' => 'required',
            'sandbox_check' => 'required',
            'merchant_id' => 'required',
            'salt_key' => 'required',
            'salt_index' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $information = [
            'merchant_id' => $request->merchant_id,
            'sandbox_check' => $request->sandbox_check,
            'salt_key' => $request->salt_key,
            'salt_index' => $request->salt_index,
        ];

        // Retrieve existing or create new PaymentGateway with keyword 'phonepe'
        $phonepe = PaymentGateway::firstOrNew(['keyword' => 'phonepe']);

        // If new record, set default values explicitly
        if (! $phonepe->exists) {
            $phonepe->keyword = 'phonepe';          
            $phonepe->name = 'PhonePe';            
            $phonepe->status = 1;    
        }

        // Update status and information from request
        $phonepe->status = $request->status ?? $phonepe->status;
        $phonepe->information = json_encode($information);

        $phonepe->save();

        Session::flash('success', __("Updated successfully!"));

        return redirect()->back();
    }
}
