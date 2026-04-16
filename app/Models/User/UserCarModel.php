<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCarModel extends Model
{
    use HasFactory;

    protected $table = 'user_car_model';

    protected $fillable = ['brand_id', 'language_id', 'name'];

    public function brand()
    {
        return $this->belongsTo(UserCarBrand::class, 'brand_id');
    }

    public function versions()
    {
        return $this->hasMany(UserCarVersion::class, 'model_id');
    }
}