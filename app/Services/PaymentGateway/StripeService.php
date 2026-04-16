<?php

namespace App\Services\PaymentGateway;


use App\Models\PaymentGateway;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\Models\User\PaymentGateway as UserPaymentGateway;

class StripeService extends PaymentService
{
  /** 
   * Set payment gateway credentials for admin and tenant
   * @param $isAdmin true or false
   * @param $tenantId  it's nullable  
   */
  public function setCredentials($isAdmin = true, $tenantId = null)
  {

    $data = $this->getGatewayData('stripe', $isAdmin, $tenantId);


    if (!$data) {
      throw new \Exception('Stripe credentials not found.');
    }

    $stripeConf = json_decode($data->information, true);
    config([

      'services.stripe.key' =>  $stripeConf["key"],
      'services.stripe.secret' => $stripeConf["secret"],
    ]);
  }

  public function createToken($cardDetails)
  {
    $stripe = Stripe::make(config()->get('services.stripe.secret'));
    return $stripe->tokens()->create(['card' => $cardDetails]);
  }
  // public function checkCurrency($be)
  // {
  //   if ($be->base_currency_text != "USD") {
  //     session()->flash('warning', __('Please select USD Currency for stripe payment.'));
  //     return false;
  //   }
  //   return true;
  // }

  public function createCharge($tokenId, $amount, $currency, $description)
  {
    $stripe = Stripe::make(config()->get('services.stripe.secret'));
    return $stripe->charges()->create([
      'card' => $tokenId,
      'currency' => $currency,
      'amount' => round($amount, 2),
      'description' => $description,
    ]);
  }
}
