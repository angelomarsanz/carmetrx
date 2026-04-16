<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\MyFatoorahController as PaymentMyFatoorahController;
use App\Models\PaymentInvoice;
use Basel\MyFatoorah\MyFatoorah;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MyFatoorahController extends Controller

{
    public function callback(Request $request)
    {
    
        $type = Session::get('myfatoorah_payment_type');
        if ($type == 'buy_plan') {
            $data = new PaymentMyFatoorahController();
            $data = $data->successPayment($request);
            Session::forget('myfatoorah_payment_type');
            if ($data['status'] == 'success') {
                return redirect()->route('success.page');
            } else {
                $cancel_url = Session::get('cancel_url');
                return redirect($cancel_url);
            }
        } 

    }

    public function cancel()
    {
        return 'cancel';
    }
}
