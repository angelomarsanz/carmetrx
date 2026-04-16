<?php

namespace App\Models\User\HomePage;

use App\Constants\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlider extends Model
{
    use HasFactory;
    public $table = 'user_hero_sliders';
    protected $guarded = [];

    public function deleteSlider()
    {
        @unlink(public_path(Constant::WEBSITE_SLIDER_IMAGE . '/' . $this->image));
        $this->delete();
        return;
    }
}
