<?php

namespace App\Services\PaymentGateway;

use Omnipay\Omnipay;

class AuthorizenetService extends PaymentService
{
  protected $gateway;

  public function setCredentials($isAdmin = true, $tenantId = null)
  {
    $data = $this->getGatewayData('authorize.net', $isAdmin, $tenantId);


    if (!$data) {
      throw new \Exception('Flutterwave credentials not found.');
    }

    $paydata = $data->convertAutoData();
    $this->gateway = Omnipay::create('AuthorizeNetApi_Api');
    $this->gateway->setAuthName($paydata['login_id']);
    $this->gateway->setTransactionKey($paydata['transaction_key']);
    if ($paydata['sandbox_check'] == 1) {
      $this->gateway->setTestMode(true);
    }
  }



  public function makePayment($request, $amount, $transactionId, $webCuryency)
  {

    // Generate a unique merchant site transaction ID.
    $transactionId = rand(100000000, 999999999);
    $response = $this->gateway->authorize([
      'amount' => $amount,
      'currency' => $webCuryency,
      'transactionId' => $transactionId,
      'opaqueDataDescriptor' => $request->input('opaqueDataDescriptor'),
      'opaqueDataValue' => $request->input('opaqueDataValue'),
    ])->send();
    return $response;
  }

  public function verifyPayment($transactionReference, $amount, $webCuryency)
  {
    $response = $this->gateway->capture([
      'amount' => $amount,
      'currency' => $webCuryency,
      'transactionReference' => $transactionReference,
    ])->send();
    return $response;
  }
}
