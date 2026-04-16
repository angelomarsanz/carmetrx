<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalSection extends Model
{
    use HasFactory;
    protected $table = "user_additional_sections";
    protected $fillable = [
        'user_id',
        'serial_number',
        'page_type',
        'possition'
    ];

    public function page_content()
    {
        return $this->belongsTo(AdditionalSectionContent::class, 'addition_section_id', 'id');
    }
}
