<?php

namespace App\Models\User\HomePage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterInformation extends Model
{
    use HasFactory;
    public $table = "user_counter_information";
    protected $guarded = [];
}
