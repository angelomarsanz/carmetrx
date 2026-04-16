<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTypeContent extends Model
{
    use HasFactory;
    public $table = "user_project_type_contents";
    protected $guarded = [];

    public static function storeTypeContent($tenantId, $projectTypeId, $requestData)
    {
        return self::create([

            'project_type_id' => $projectTypeId,
            'user_id' => $tenantId,
            'language_id' => $requestData['language_id'],
            'title' => $requestData['title'],
            'unit' => $requestData['unit'],
            'min_area' =>  $requestData['min_area'],
            'max_area' =>  $requestData['max_area'],
            'min_price' =>  $requestData['min_price'],
            'max_price' =>  $requestData['max_price'],
        ]);
    }

    public static function updateTypeContent($tenantId, $projectTypeId, $requestData)
    {
        return self::updateOrCreate([
            'project_type_id' => $projectTypeId,
            'user_id' => $tenantId,
            'language_id' => $requestData['language_id'],
        ], [

            'title' => $requestData['title'],
            'unit' => $requestData['unit'],
            'min_area' =>  $requestData['min_area'],
            'max_area' =>  $requestData['max_area'],
            'min_price' =>  $requestData['min_price'],
            'max_price' =>  $requestData['max_price'],
        ]);
    }
}
