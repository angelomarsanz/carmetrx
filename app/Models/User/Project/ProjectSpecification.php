<?php

namespace App\Models\User\Project;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSpecification extends Model
{
    use HasFactory;
    public $table = "user_project_specifications";
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(ProjectSpecificationContent::class, 'project_spacification_id', 'id');
    }

    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }

    public static function storeSpecification($tenantID, $projectId, $key)
    {

        return self::create([
            'user_id' => $tenantID,
            'project_id' => $projectId,
            'key' => $key,
        ]);
    }

    public function deleteSpecification()
    {

        if (!$this) {
            throw new Exception('No project specification found.');
        }
        $contents = $this->contents()->get();
        foreach ($contents as $content) {
            $content->delete();
        }

        $this->delete();

        return;
    }
}
