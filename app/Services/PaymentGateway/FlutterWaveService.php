<?php

namespace App\Services\PaymentGateway;


class FlutterWaveService extends PaymentService
{
  protected $public_key, $secret_key;

  public function setCredentials($isAdmin = true, $tenantId = null)
  {
    $data = $this->getGatewayData('flutterwave', $isAdmin, $tenantId);


    if (!$data) {
      throw new \Exception('Flutterwave credentials not found.');
    }

    $paydata = $data->convertAutoData();
    $this->public_key = $paydata['public_key'];
    $this->secret_key = $paydata['secret_key'];
  }



  public function makePayment($amount, $email, $successUrl, $cancelUrl, $item_number, $webCuryency)
  {
    $cancel_url = $cancelUrl;
    $notify_url = $successUrl;
    // Session::put('request', $request->all());
    // Session::put('payment_id', $_item_number);

    // SET CURL

    $curl = curl_init();
    $currency = $webCuryency;
    $txref = $item_number; // ensure you generate unique references per transaction.
    $PBFPubKey = $this->public_key; // get your public key from the dashboard.
    $redirect_url = $notify_url;


    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/payments",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode([
        'amount' => $amount,
        'customer' => [
          'email' => $email,
        ],
        'payment_options' => 'card,banktransfer',
        'currency' => $currency,
        'tx_ref' => $txref,
        'redirect_url' => $redirect_url,
      ]),
      CURLOPT_HTTPHEADER => [
        'authorization: Bearer ' . $this->secret_key,
        "content-type: application/json",
        "cache-control: no-cache"
      ],
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $transaction = json_decode($response, true);

    if ($err) {
      // there was an error contacting the rave API
      return redirect($cancel_url)->with('error', 'Curl returned error: ' . $err);
    }


    // redirect to payment
    if ($transaction['status'] === 'success') {
      return redirect($transaction['data']['link']);
    } else {
      return redirect()->back()->with('error', 'Error: ' . $transaction['message'])->withInput();
    }
  }

  public function verifyPayment($txId)
  {
    $ch = curl_init("https://api.flutterwave.com/v3/transactions/{$txId}/verify");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'authorization: Bearer ' . $this->secret_key,
      'Content-Type: application/json'
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    return $resp = json_decode($response, true);
  }
}
