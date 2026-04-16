<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpInformation extends Model
{
    use HasFactory;
    public $table = "user_smtp_info";
    protected $guarded = [];
}
