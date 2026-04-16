<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Amenity extends Model
{
    use HasFactory;
    public $table = "user_amenities";
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(AmenityContent::class, 'amenity_id', 'id');
    }
    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }

    public function amenityContent()
    {
        return $this->hasOne(AmenityContent::class, 'amenity_id', 'id');
    }
    public function propertyAmenities()
    {
        return $this->hasMany(PropertyAmenity::class, 'amenity_id', 'id');
    }

    public function deleteAmenity()
    {
        $amenity = $this;
        $delete = false;
        if ($amenity->propertyAmenities()->count() ==  0) {
            $delete = true;
            DB::transaction(function () use ($amenity) {
                $amenity->contents()->delete();
                $amenity->delete();
            });
        }
        return $delete;
    }
}
