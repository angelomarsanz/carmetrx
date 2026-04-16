<?php

namespace App\Models\User\Property;

use Mews\Purifier\Facades\Purifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyContent extends Model
{
    use HasFactory;
    public $table = "user_property_contents";
    protected $guarded = [];

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => make_slug($value),
        );
    }


    protected function description(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Purifier::clean($value, 'youtube'),
        );
    }


    public static function storePropertyContent($propertyId, $requestData)
    {
        return self::create([
            'property_id' => $propertyId,
            'language_id' => $requestData['language_id'],
            'title' => $requestData['title'],
            'slug' => $requestData['slug'],
            'summary' => $requestData['summary'],
            'address' => $requestData['address'],
            'description' => $requestData['description'],
            'meta_keyword' => $requestData['meta_keyword'],
            'meta_description' => $requestData['meta_description'],
        ]);
    }

    public static function updateOrCreatePropertyContent($propertyId, $requestData)
    {
        return self::updateOrCreate([
            'property_id' => $propertyId,
            'language_id' => $requestData['language_id'],
        ], [
            'title' => $requestData['title'],
            'slug' => $requestData['slug'],
            'summary' => $requestData['summary'],
            'address' => $requestData['address'],
            'description' => $requestData['description'],
            'meta_keyword' => $requestData['meta_keyword'],
            'meta_description' => $requestData['meta_description'],
        ]);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }
    public function categoryContent()
    {
        return $this->belongsTo(CategoryContent::class, 'category_id', 'category_id');
    }

    public function propertySpacifications()
    {
        return $this->hasMany(PropertySpecification::class, 'property_id', 'property_id');
    }
    public function galleryImages()
    {
        return $this->hasMany(SliderImage::class, 'property_id', 'property_id');
    }
}
