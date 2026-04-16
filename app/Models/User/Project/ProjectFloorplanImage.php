<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFloorplanImage extends Model
{
    use HasFactory;
    public $table = "user_project_floorplan_images";
    protected $guarded = [];
}
