<?php

namespace App\Services\PaymentGateway;



class MercadopagoService extends PaymentService
{
  private $access_token;
  private $sandbox;

  /** 
   * Set payment gateway credentials for admin and tenant
   * @param $isAdmin true or false
   * @param $tenantId  it's nullable  
   */
  public function setCredentials($isAdmin = true, $tenantId = null)
  {
    $data = $this->getGatewayData('mercadopago', $isAdmin, $tenantId);

    if (!$data) {
      throw new \Exception('Mercadopago credentials not found.');
    }

    $paydata = $data->convertAutoData();
    $this->access_token = $paydata['token'];
    $this->sandbox = $paydata['sandbox_check'];
  }



  public function makePayment($title, $description, $amount, $email,  $success_url, $cancel_url)
  {

    $return_url = $success_url;
    $cancel_url = $cancel_url;
    $notify_url = $cancel_url;

    $curl = curl_init();
    $preferenceData = [
      'items' => [
        [
          'id' => uniqid("mercadopago-"),
          'title' => $title,
          'description' => $description,
          'quantity' => 1,
          'currency_id' => "BRL", //unfortunately mercadopago only support BRL currency
          'unit_price' => round($amount, 2),
        ]
      ],
      'payer' => [
        'email' => $email,
      ],
      'back_urls' => [
        'success' => $return_url,
        'pending' => '',
        'failure' => $cancel_url,
      ],
      'notification_url' => $notify_url,
      'auto_return' => 'approved',

    ];

    $httpHeader = [
      "Content-Type: application/json",
    ];
    $url = "https://api.mercadopago.com/checkout/preferences?access_token=" . $this->access_token;
    $opts = [
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($preferenceData, true),
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTPHEADER => $httpHeader
    ];

    curl_setopt_array($curl, $opts);
    $response = curl_exec($curl);
    $payment = json_decode($response, true);
    $err = curl_error($curl);
    curl_close($curl);


    // Session::put('request', $request->all());
    // Session::put('success_url', $_success_url);
    // Session::put('cancel_url', $_cancel_url);
    if ($this->sandbox == 1) {
      return redirect($payment['sandbox_init_point']);
    } else {
      return redirect($payment['init_point']);
    }
  }

  public function verifyPayment($paymentId)
  {
    $url = "https://api.mercadopago.com/v1/payments/" . $paymentId . "?access_token=" . $this->access_token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $paymentData = curl_exec($ch);
    curl_close($ch);
    return $paymentData;
  }
}
