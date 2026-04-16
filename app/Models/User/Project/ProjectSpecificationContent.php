<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSpecificationContent extends Model
{
    use HasFactory;
    public $table = "user_project_specification_contents";
    protected $guarded = [];

    public static function storeSpecificationContent($specificationId, $requestData)
    {

        return self::create([
            'project_spacification_id' => $specificationId,
            'language_id' => $requestData['language_id'],
            'label' => $requestData['label'],
            'value' => $requestData['value'],
        ]);
    }
}
