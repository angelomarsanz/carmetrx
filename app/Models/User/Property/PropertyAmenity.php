<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAmenity extends Model
{
    use HasFactory;
    public $table = "user_property_amenities";
    protected $guarded = [];

    public function amenity()
    {
        return $this->belongsTo(Amenity::class, 'amenity_id', 'id');
    }
    public function amenityContent()
    {
        return $this->hasOne(AmenityContent::class, 'amenity_id', 'amenity_id');
    }
}
