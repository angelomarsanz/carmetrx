<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasFactory;

    public $table = "user_project_categories";
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(CategoryContent::class, 'category_id', 'id');
    }
    public function categoryContent()
    {
        return $this->hasOne(CategoryContent::class, 'category_id', 'id');
    }
    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }
    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id', 'id');
    }
    public function scopeGetCategories($query, $tenantId, $languageId, $name = null)
    {
        return $query->where('user_id', $tenantId)
            ->when($name, function ($query, $name) {
                $query->whereHas('contents', function ($q) use ($name) {
                    $q->where('name', 'LIKE', "%{$name}%");
                });
            })
            ->orderBy('serial_number', 'asc')
            ->get()
            ->map(function ($item) use ($languageId) {
                $content = $item->getContent($languageId);
                $item->name = optional($content)->name;
                return $item;
            })->filter(function ($item) {
                return $item->name !== null;
            });
    }
    public function deleteCategory()
    {
        $category = $this;
        $delete = false;
        if ($category->projects()->count() ==  0) {
            $delete = true;
            DB::transaction(function () use ($category) {
                @unlink(public_path('assets/img/project-category/') . $category->image);
                $category->contents()->delete();
                $category->delete();
            });
        }
        return $delete;
    }
}
