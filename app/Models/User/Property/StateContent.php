<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateContent extends Model
{
    use HasFactory;
    public $table = "user_state_contents";
    protected $guarded = [];
}
