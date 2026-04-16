<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryContent extends Model
{
    use HasFactory;
    public $table = "user_property_category_contents";
    protected $guarded = [];

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => make_slug($value),

        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
