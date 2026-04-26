<?php

namespace App\Models\User\Property;

use App\Models\User;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;
    public $table = "user_properties";
    protected $guarded = [];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return  $this->belongsTo(Country::class);
    }

    protected function isCountryActive(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                $county_status = $this->basicInfo->property_country_status; // Change this to match your attribute name

                $attributeName = 'country_id';
                // Check if the attribute exists and is not null
                if ($this->attributes[$attributeName] && $county_status == 1) {

                    return true;
                }

                return false; // Return false if the attribute is null or doesn't exist
            }
        );
    }

    public function state()
    {
        return  $this->belongsTo(State::class);
    }


    protected function isStateActive(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                $county_status = $this->basicInfo->property_state_status; // Change this to match your attribute name

                $attributeName = 'state_id';
                // Check if the attribute exists and is not null
                if ($this->attributes[$attributeName] && $county_status == 1) {

                    return true;
                }

                return false; // Return false if the attribute is null or doesn't exist
            },
        );
    }


    public static function storeProperty($tenantId, $requestData)
    {
        return self::create([
            'user_id' => $tenantId,
            'agent_id' => $requestData['agent_id'] ?? 0,
            'category_id' => $requestData['category_id'],
            'country_id' => $requestData['country_id'] ?? null,
            'state_id' => $requestData['state_id'] ?? null,
            'city_id' => $requestData['city_id'] ?? null,
            'featured_image' => $requestData['featuredImgName'],
            'floor_planning_image' => $requestData['floorPlanningImage'],
            'video_image' => $requestData['videoImage'],
            'price' => $requestData['price'],
            'purpose' => $requestData['purpose'],
            'type' => $requestData['type'],
            'km' => $requestData['km'] ?? null,
            'year' => $requestData['year'] ?? null,
            'area' => $requestData['area'],
            'video_url' => $requestData['video_url'],
            'status' => $requestData['status'],
            'latitude' => $requestData['latitude'],
            'longitude' => $requestData['longitude'],
            'currency_symbol' => $requestData['currency_symbol'],
            'brand_id' => $requestData['brand_id'],
            'model_id' => $requestData['model_id'],
            'version_id' => $requestData['version_id'] ?? null,
            'transmision' => $requestData['transmision'] ?? null,
            'color' => $requestData['color'] ?? null,
            'traction' => $requestData['traction'] ?? null,
            'engine' => $requestData['engine'] ?? null,
            'cost' => $requestData['cost'] ?? null,
            'shared' => isset($requestData['share']) ? 1 : 0
        ]);
    }

    public function updateProperty($requestData)
    {
        return $this->update([

            'agent_id' => $requestData['agent_id'] ?? 0,
            'category_id' => $requestData['category_id'],
            'country_id' => $requestData['country_id'] ?? null,
            'state_id' => $requestData['state_id'] ?? null,
            'city_id' => $requestData['city_id'] ?? null,
            'featured_image' => $requestData['featuredImgName'],
            'floor_planning_image' => $requestData['floorPlanningImage'],
            'video_image' => $requestData['videoImage'],
            'price' => $requestData['price'],
            'purpose' => $requestData['purpose'],
            'type' => $requestData['type'],
            'km' => $requestData['km'] ?? null,
            'year' => $requestData['year'] ?? null,
            'area' => $requestData['area'],
            'video_url' => $requestData['video_url'],
            'status' => $requestData['status'],
            'latitude' => $requestData['latitude'],
            'longitude' => $requestData['longitude'],
            'currency_symbol' => $requestData['currency_symbol'],
            'brand_id' => $requestData['brand_id'],
            'model_id' => $requestData['model_id'],
            'version_id' => $requestData['version_id'] ?? null,
            'transmision' => $requestData['transmision'] ?? null,
            'color' => $requestData['color'] ?? null,
            'traction' => $requestData['traction'] ?? null,
            'engine' => $requestData['engine'] ?? null,
            'cost' => $requestData['cost'] ?? null,
            'shared' => isset($requestData['share']) ? 1 : 0
        ]);
    }
    public  function destroyPropertry()
    {

        DB::transaction(function () {
            $property = $this;

            // ===== delete message under property =====
            $property->propertyContacts()->delete();

            // ===== delete amenities under property =====
            $property->proertyAmenities()->delete();

            // ===== delete wishlists under property =====
            $property->wishlists()->delete();

            // ===== delete property specification =====
            $specifications = $property->specifications()->get();
            foreach ($specifications as $specification) {

                $specification->deleteSpecification();
            }

            // ===== delete propety contents =====
            $propertyContents = $property->contents()->get();
            foreach ($propertyContents as $content) {
                $content->delete();
            }

            // ===== delete gallery images under property =====
            $propertySliderImages  = $property->galleryImages()->get();
            foreach ($propertySliderImages  as  $image) {

                @unlink(public_path('assets/img/property/slider-images/' . $image->image));
                $image->delete();
            }
            if (!is_null($property->featured_image)) {
                @unlink(public_path('assets/img/property/featureds/' . $property->featured_image));
            }

            if (!is_null($property->floor_planning_image)) {
                @unlink(public_path('assets/img/property/plannings/' . $property->floor_planning_image));
            }
            if (!is_null($property->video_image)) {
                @unlink(public_path('assets/img/property/video/' . $property->video_image));
            }
            $property->delete();
        });
        return;
    }

    public function propertyContacts()
    {
        return $this->hasMany(PropertyContact::class, 'property_id', 'id');
    }

    public  function basicInfo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                return BasicSetting::where('user_id', $this->user_id)->first();
            },
        );
    }

    public function contents()
    {
        return $this->hasMany(PropertyContent::class, 'property_id');
    }

    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }

    public function cityContent($langId)
    {
        return $this->belongsTo(CityContent::class, 'city_id', 'city_id')->where('language_id', $langId)->first();
    }
    public function galleryImages()
    {
        return $this->hasMany(SliderImage::class, 'property_id', 'id');
    }

    public function propertyAmenities()
    {
        return $this->hasMany(PropertyAmenity::class, 'property_id', 'id');
    }

    public function user()
    {
        return  $this->belongsTo(User::class, 'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }


    protected function authorIsAgent(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                if ($this->attributes['agent_id'] != 0 || $this->attributes['agent_id'] != null) {

                    return true;
                }

                return false; // Return false if the attribute is null or 0
            }
        );
    }

    public function categoryContent()
    {
        return $this->belongsTo(CategoryContent::class, 'category_id', 'category_id');
    }

    public function specifications()
    {
        return $this->hasMany(PropertySpecification::class, 'property_id', 'id');
    }


    public function proertyAmenities()
    {
        return $this->hasMany(PropertyAmenity::class, 'property_id', 'id');
    }

    public function featureds()
    {
        return $this->hasMany(self::class, 'id')->where('featured', 1);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'property_id', 'id');
    }
}
