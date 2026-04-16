<?php

namespace App\Models\User;

use App\Constants\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
  use HasFactory;

  public $table = "user_advertisements";

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'ad_type',
    'user_id',
    'resolution_type',
    'image',
    'url',
    'slot',
    'views'
  ];

  public function advertisementDelete()
  {
    @unlink(public_path(Constant::WEBSITE_ADVERTISEMENT_IMAGE . '/' . $this->image));
    $this->delete();
    return;
  }
}
