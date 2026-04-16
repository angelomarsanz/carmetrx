<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class AdditionalSectionContent extends Model
{
    use HasFactory;

    protected $table = 'user_additional_section_contents';

    protected $fillable = [
        'language_id',
        'addition_section_id',
        'section_name',
        'content'
    ];

    protected function content(): Attribute
    {
        return Attribute::make(
            set: fn($value) =>  Purifier::clean($value, 'youtube'),
        );
    }
}
