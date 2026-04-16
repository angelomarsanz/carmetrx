<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryContent extends Model
{
    use HasFactory;
    public $table = "user_project_country_contents";
    protected $guarded = [];
     
}
