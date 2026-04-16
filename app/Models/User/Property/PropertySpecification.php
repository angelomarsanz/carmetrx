<?php

namespace App\Models\User\Property;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySpecification extends Model
{
    use HasFactory;

    public $table = "user_property_specifications";
    protected $guarded = [];


    public static function storeSpecification($tenantID, $propertyId, $key)
    {

        return self::create([
            'user_id' => $tenantID,
            'property_id' => $propertyId,
            'key' => $key,
        ]);
    }

    public function contents()
    {
        return $this->hasMany(PropertySpecificationContent::class, 'property_spacification_id', 'id');
    }

    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }

    public function deleteSpecification()
    {

        if (!$this) {
            throw new Exception('No property specification found.');
        }
        $contents = $this->contents()->get();
        foreach ($contents as $content) {
            $content->delete();
        }

        $this->delete();

        return;
    }
}
