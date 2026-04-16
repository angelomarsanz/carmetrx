<?php

namespace App\Services\PaymentGateway;



class PaystackService extends PaymentService
{

  private  $secret_key;
  public function setCredentials($isAdmin = true, $tenantId = null)
  {
    $data = $this->getGatewayData('paystack', $isAdmin, $tenantId);

    if (!$data) {
      throw new \Exception('Paystack credentials not found.');
    }

    $paydata = $data->convertAutoData();
    $this->secret_key = $paydata['key'];
  }

  public function makePayment($amount, $email, $success_url, $cancle_url)
  {
    $secret_key = $this->secret_key;

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode([
        'amount' => $amount * 100,
        'email' => $email,
        'callback_url' => $success_url
      ]),
      CURLOPT_HTTPHEADER => [
        "authorization: Bearer " . $secret_key, //replace this with your own test key
        "content-type: application/json",
        "cache-control: no-cache"
      ],
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    if ($err) {
      return redirect($cancle_url)->with('error', $err);
    }

    $tranx = json_decode($response, true);
    if (!$tranx['status']) {
      return redirect($cancle_url)->with("error", $tranx['message']);
    }
    return redirect($tranx['data']['authorization_url']);
  }
}
