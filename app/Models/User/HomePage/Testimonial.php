<?php

namespace App\Models\User\HomePage;

use App\Constants\Constant;
use App\Models\User\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
  use HasFactory;

  protected $table = 'user_testimonials';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'language_id',
    'user_id',
    'image',
    'name',
    'occupation',
    'rating',
    'comment',
    'serial_number'
  ];


  public function deleteTestimonial()
  {
    @unlink(public_path(Constant::WEBSITE_TESTIMONIAL_IMAGE . '/' . $this->image));
    $this->delete();
    return;
  }

  public function language()
  {
    return $this->belongsTo(Language::class, 'language_id');
  }
}
