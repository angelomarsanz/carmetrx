<?php

namespace App\Models\User\Journal;

use App\Models\User\Language;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlogCategory extends Model
{
  use HasFactory;

  protected $table = 'user_blog_categories';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['user_id', 'status', 'serial_number'];


  public  function scopeGetCategories($query, $tenantId, $languageId)
  {


    return $query->where('user_id', $tenantId)->orderBy('serial_number', 'asc')->get()
      ->map(function ($item) use ($languageId) {
        $content = $item->getContent($languageId);
        $item->name = optional($content)->name;
        $item->slug = optional($content)->slug;
        return $item;
      })->filter(function ($item) {
        return $item->name !== null;
      });
  }
  public function contents(): HasMany
  {
    return $this->hasMany(BlogCategoryContent::class, 'category_id', 'id');
  }
  public function getContent($langId)
  {
    return $this->contents()->where('language_id', $langId)->first();
  }

  public function blogs()
  {
    return $this->hasMany(Blog::class, 'category_id');
  }

  public function deleteCategory()
  {
    try {
      DB::beginTransaction();
      $category = $this;
      $contents =  $category->contents();
      foreach ($contents as $content) {
        $content->delete();
      }
      $category->delete();
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
    }
    return;
  }
}
