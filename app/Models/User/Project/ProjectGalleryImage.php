<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectGalleryImage extends Model
{
    use HasFactory;
    public $table = "user_project_gallery_images";
    protected $guarded = [];
}
