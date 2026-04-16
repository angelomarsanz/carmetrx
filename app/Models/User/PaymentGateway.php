<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    public $timestamps = false;

    public $table = "user_payment_gateways";

    protected $fillable = [
        'user_id',
        'title',
        'website_id',
        'details',
        'subtitle',
        'name',
        'type',
        'keyword',
        'information',
        'status'
    ];

    public function convertAutoData()
    {
        return json_decode($this->information, true);
    }

    public function getAutoDataText()
    {
        $text = $this->convertAutoData();
        return end($text);
    }

    public function showKeyword()
    {
        return $this->keyword == null ? 'other' : $this->keyword;
    }

    public function showForm()
    {
        $data = $this->keyword == null ? 'other' : $this->keyword;
        $values = ['paypal'];
        if (in_array($data, $values)) {
            $show = 'no';
        } else {
            $show = 'yes';
        }
        return $show;
    }
}
