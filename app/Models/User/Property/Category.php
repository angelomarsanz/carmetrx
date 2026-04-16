<?php

namespace App\Models\User\Property;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    public $table = "user_property_categories";
    protected $guarded = [];

    public function categoryContents()
    {
        return $this->hasMany(CategoryContent::class, 'category_id', 'id');
    }
    public function categoryContent()
    {
        return $this->hasOne(CategoryContent::class, 'category_id', 'id');
    }
    public function getContent($langId)
    {
        return $this->categoryContents()->where('language_id', $langId)->first();
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'category_id', 'id');
    }


    public function deleteCategory()
    {
        $category = $this;
        $delete = false;
        if ($category->properties()->count() ==  0) {
            $delete = true;
            DB::transaction(function () use ($category) {
                @unlink(public_path('assets/img/property-category/') . $category->image);
                $category->categoryContents()->delete();
                $category->delete();
            });
        }
        return $delete;
    }
}
