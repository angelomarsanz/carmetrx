<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCarBrand extends Model
{
    use HasFactory;

    protected $table = 'user_car_brand';

    protected $fillable = ['language_id', 'name'];

    public function models()
    {
        return $this->hasMany(UserCarModel::class, 'brand_id');
    }
}