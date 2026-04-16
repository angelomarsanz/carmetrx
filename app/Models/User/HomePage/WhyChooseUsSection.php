<?php

namespace App\Models\User\HomePage;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class WhyChooseUsSection extends Model
{
    use HasFactory;

    public $table = 'user_why_choose_us_sections';
    protected $guarded = [];

    protected function description(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Purifier::clean($value),

        );
    }
}
