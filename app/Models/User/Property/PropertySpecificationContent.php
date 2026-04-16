<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySpecificationContent extends Model
{
    use HasFactory;
    public $table = "user_property_specification_contents";
    protected $guarded = [];

    public static function storeSpecificationContent($specificationId, $requestData)
    {

        return self::create([
            'property_spacification_id' => $specificationId,
            'language_id' => $requestData['language_id'],
            'label' => $requestData['label'],
            'value' => $requestData['value'],
        ]);
    }
}
