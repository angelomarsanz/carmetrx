<?php

namespace App\Models\User\HomePage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;
    public $table = 'user_partners';
    protected $guarded = [];
}
