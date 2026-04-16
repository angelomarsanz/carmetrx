<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCarVersion extends Model
{
    use HasFactory;

    protected $table = 'user_car_version';

    protected $fillable = ['brand_id', 'model_id', 'language_id', 'name'];

    public function brand()
    {
        return $this->belongsTo(UserCarBrand::class, 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(UserCarModel::class, 'model_id');
    }
}