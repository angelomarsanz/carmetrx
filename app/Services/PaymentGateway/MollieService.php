<?php

namespace App\Services\PaymentGateway;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Mollie\Laravel\Facades\Mollie;

class MollieService extends PaymentService
{
  protected $public_key;

  public function setCredentials($isAdmin = true, $tenantId = null)
  {
    $data = $this->getGatewayData('mollie', $isAdmin, $tenantId);

    if (!$data) {
      throw new \Exception('Flutterwave credentials not found.');
    }

    $paydata = $data->convertAutoData();
    $this->public_key = $paydata['key'];
    Config::set('mollie.key', $paydata['key']);
  }

  public function makePayment($amount, $success_url, $title, $webCuryency)
  {

    $notify_url = $success_url;
    $payment = Mollie::api()->payments->create([
      'amount' => [
        'currency' => $webCuryency,
        'value' => '' . sprintf('%0.2f', $amount) . '',
      ],
      'description' => $title,
      'redirectUrl' => $notify_url,
    ]);
    // Update the redirect URL with the payment ID
    $payment->redirectUrl = $notify_url . '?payment_id=' . $payment->id;
    $payment->update();

    $payment = Mollie::api()->payments->get($payment->id);

    return redirect($payment->getCheckoutUrl(), 303);
  }

  public function verifyPayment($paymentId, $cancleUrl)
  {

    try {
      $payment = Mollie::api()->payments->get($paymentId);

      return $payment;
    } catch (\Exception $e) {
      return redirect($cancleUrl);
    }
  }
}
