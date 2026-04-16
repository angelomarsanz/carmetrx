<?php

namespace App\Models\User\Journal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Blog extends Model
{
  use HasFactory;

  public $table = "user_blogs";

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['user_id', 'category_id', 'image', 'serial_number'];

  public function informations(): HasMany
  {
    return $this->hasMany(BlogInformation::class);
  }
  public function information($lagId)
  {
    return  $this->informations()->where('language_id', $lagId)->first();
  }

  public function categoryContent($lagId)
  {
    return $this->hasOne(BlogCategoryContent::class, 'category_id', 'category_id')->where('language_id', $lagId)->first();
  }


  public function scopeRecentBlogs($query, int  $tenantId, int $langId, int $Nitem = 3)
  {
    return $query->where('user_blogs.user_id', $tenantId)
      ->join('user_blog_informations', 'user_blogs.id', '=', 'user_blog_informations.blog_id')
      ->where('user_blog_informations.language_id', '=', $langId)
      ->select('user_blogs.image',  'user_blog_informations.title', 'user_blog_informations.slug', 'user_blog_informations.author', 'user_blogs.created_at', 'user_blog_informations.content')
      ->latest()
      ->limit($Nitem)->get();
  }
}
