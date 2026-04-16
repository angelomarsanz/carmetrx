<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateContent extends Model
{
    use HasFactory;
    public $table = "user_project_state_contents";
    protected $guarded = [];
    
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => make_slug($value),

        );
    }

}
