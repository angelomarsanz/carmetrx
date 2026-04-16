<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
  use HasFactory;

  protected $table = 'user_faqs';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['language_id',   'user_id', 'question', 'answer', 'serial_number'];

  public function faqLang()
  {
    return $this->belongsTo(Language::class, 'language_id');
  }
}
