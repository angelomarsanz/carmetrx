<?php

namespace App\Models\User;

use App\Constants\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
  use HasFactory;

  protected $table = 'user_popups';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'language_id',
    'user_id',
    'website_id',
    'type',
    'image',
    'name',
    'background_color',
    'background_color_opacity',
    'title',
    'text',
    'button_text',
    'button_color',
    'button_url',
    'end_date',
    'end_time',
    'delay',
    'serial_number',
    'status'
  ];

  public function popupLang()
  {
    return $this->belongsTo(Language::class, 'language_id');
  }

  public function popupDelete()
  {
    @unlink(public_path(Constant::WEBSITE_ANNOUNCEMENT_POPUP_IMAGE . '/' . $this->image));
    $this->delete();
    return;
  }
}
