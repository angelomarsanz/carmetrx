<?php

namespace App\Models\User\Journal;

use App\Models\User\Language;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;

class BlogInformation extends Model
{
  use HasFactory;

  protected $table = 'user_blog_informations';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'language_id',
    'user_id', 
    'blog_id',
    'title',
    'slug',
    'author',
    'content',
    'meta_keywords',
    'meta_description'
  ];

  protected function slug(): Attribute
  {
    return Attribute::make(
      set: fn($value) => make_slug($value),

    );
  }

  protected function content(): Attribute
  {
    return Attribute::make(
      set: fn($value) => Purifier::clean($value, 'youtube'),

    );
  }
  public function language()
  {
    return $this->belongsTo(Language::class, 'language_id');
  }

  public function blogCategory()
  {
    return $this->belongsTo(BlogCategory::class, 'blog_category_id');
  }

  public function blog()
  {
    return $this->belongsTo(Blog::class, 'blog_id');
  }
}
