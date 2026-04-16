<?php

namespace App\Services\PaymentGateway;

use Illuminate\Support\Facades\Session;
use App\Http\Helpers\Instamojo;
use Razorpay\Api\Errors\SignatureVerificationError;

class InstamojoService extends PaymentService
{

  protected $api;
  public function setCredentials($isAdmin = true, $tenantId = null)
  {
    $data = $this->getGatewayData('instamojo', $isAdmin, $tenantId);

    if (!$data) {
      throw new \Exception('Instamojo credentials not found.');
    }

    $paydata = $data->convertAutoData();

    if ($paydata['sandbox_check'] == 1) {
      $this->api = new Instamojo($paydata['key'], $paydata['token'], 'https://test.instamojo.com/api/1.1/');
    } else {
      $this->api = new Instamojo($paydata['key'], $paydata['token']);
    }
  }

  public function makePayment($title, $amount,  $success_url, $cancel_url)
  {

    try {

      $response = $this->api->paymentRequestCreate(array(
        "purpose" => $title,
        "amount" => $amount,
        "send_email" => false,
        "email" => null,
        "redirect_url" => $success_url
      ));

      $redirect_url = $response['longurl'];

      Session::put('payment_id', $response['id']);


      return redirect($redirect_url);
    } catch (\Exception $e) {
      return redirect($cancel_url)->with('error', 'Error: ' . $e->getMessage());
    }
  }

 
}
