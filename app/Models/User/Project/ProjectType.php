<?php

namespace App\Models\User\Project;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;
    public $table = "user_project_types";
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function projectTypeContnents()
    {
        return $this->hasMany(ProjectTypeContent::class, 'project_type_id', 'id');
    }

    public function deleteType()
    {

        if (!$this) {
            throw new Exception('No project type found.');
        }
        $typeContents = $this->projectTypeContnents()->get();
        foreach ($typeContents as $content) {
            $content->delete();
        }

        $this->delete();

        return;
    }
}
