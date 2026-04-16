<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityContent extends Model
{
    use HasFactory;
    public $table = "user_city_contents";
    protected $guarded = [];

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => make_slug($value),

        );
    }
}
