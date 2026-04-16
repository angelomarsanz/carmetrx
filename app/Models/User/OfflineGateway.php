<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class OfflineGateway extends Model
{
    public $table = "user_offline_gateways";

    protected $fillable = [
        'id',
        'name',
        'short_description',
        'instructions',
        'serial_number',
        'status',
        'is_receipt',
        'receipt',
        'user_id',
        'website_id',
    ];
}
