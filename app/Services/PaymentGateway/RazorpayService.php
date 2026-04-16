<?php

namespace App\Services\PaymentGateway;

use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService extends PaymentService
{

  private  $keyId, $keySecret, $api;
  public function setCredentials($isAdmin = true, $tenantId = null)
  {
    $data = $this->getGatewayData('razorpay', $isAdmin, $tenantId);

    if (!$data) {
      throw new \Exception('Razorpay credentials not found.');
    }

    $paydata = $data->convertAutoData();

    $this->keyId = $paydata['key'];
    $this->keySecret = $paydata['secret'];
    $this->api = new Api($this->keyId, $this->keySecret);
  }

  public function makePayment($requestData, $amount, $item_number, $success_url, $cancle_url)
  {

    $notify_url = $success_url;

    $orderData = [
      'receipt' => $requestData['title'],
      'amount' => $amount * 100,
      'currency' => 'INR',
      'payment_capture' => 1 // auto capture
    ];

    $razorpayOrder = $this->api->order->create($orderData);
    // Session::put('request', $request->all());
    // Session::put('order_payment_id', $razorpayOrder['id']);
    $order_payment_id = $razorpayOrder['id'];
    $displayAmount = $amount;

    $checkout = 'automatic';

    if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true)) {
      $checkout = $_GET['checkout'];
    }



    $data = [
      "key" => $this->keyId,
      "amount" => $amount,
      "name" => $requestData['title'],
      "description" => $requestData['description'],
      "prefill" => [
        "name" => $requestData['name'],
        "email" => $requestData['email'],
        "contact" => $requestData['razorpay_phone'],
      ],
      "notes" => [
        "address" => $requestData['razorpay_address'],
        "merchant_order_id" => $item_number,
      ],
      "theme" => [
        "color" => $requestData['base_color'],
      ],
      "order_id" => $razorpayOrder['id'],
    ];



    $json = json_encode($data);
    $displayCurrency = $requestData['base_currency_text'];

    return view('front.razorpay', compact('data', 'order_payment_id', 'displayCurrency', 'json', 'notify_url'));
  }

  public function verifyPayment($request)
  {

    if (empty($request['razorpay_payment_id']) === false) {

      try {
        $attributes = array(
          'razorpay_order_id' => $request['order_payment_id'],
          'razorpay_payment_id' => $request['razorpay_payment_id'],
          'razorpay_signature' => $request['razorpay_signature']
        );

        $this->api->utility->verifyPaymentSignature($attributes);
      } catch (SignatureVerificationError $e) {
        return false;
      }
    }
    return true;
  }
}
