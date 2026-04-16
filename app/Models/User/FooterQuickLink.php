<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterQuickLink extends Model
{
    use HasFactory;

    protected $table = "user_footer_quick_links";

    protected $fillable = [
        'user_id',
        'language_id',
        'title',
        'url',
        'serial_number'
    ];

    public function quickLinkLang()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
