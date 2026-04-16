<?php

namespace App\Models\User\HomePage;

use App\Models\User\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSection extends Model
{
  use HasFactory;

  public $table = "user_newsletter_sections";

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'language_id',
    'user_id', 
    'title',
    'subtitle',
    'btn_name'
  ];

  public function language()
  {
    return $this->belongsTo(Language::class, 'language_id');
  }
}
