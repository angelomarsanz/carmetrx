<?php

namespace App\Models\User\CustomPage;

use App\Models\User\Language;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;

class PageContent extends Model
{
    use HasFactory;

    public $table = "user_page_contents";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_id',
        'user_id',
        'page_id',
        'title',
        'slug',
        'content',
        'meta_keywords',
        'meta_description'
    ];
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => make_slug($value),

        );
    }


    protected function content(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Purifier::clean($value, 'youtube'),

        );
    }
    public function contentLang(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
