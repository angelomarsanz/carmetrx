<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Mews\Purifier\Facades\Purifier;

class ProjectContent extends Model
{
    use HasFactory;
    public $table = "user_project_contents";
    protected $guarded = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => make_slug($value),

        );
    }


    protected function description(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Purifier::clean($value, 'youtube'),

        );
    }

    public static function storeProjectContent($requstData)
    {
        return self::create([
            'project_id' => $requstData['project_id'],
            'language_id' => $requstData['language_id'],
            'title' => $requstData['title'],
            'slug' => $requstData['title'],
            'address' => $requstData['address'],
            'description' => $requstData['description'],
            'meta_keyword' => $requstData['meta_keyword'],
            'meta_description' => $requstData['meta_description'],
        ]);
    }

    public static function updateOrCreateProjectContent($projectId, $requestData)
    {
        return self::updateOrCreate([
            'project_id' => $projectId,
            'language_id' => $requestData['language_id'],
        ], [
            'title' => $requestData['title'],
            'slug' => $requestData['slug'],
            'address' => $requestData['address'],
            'description' => $requestData['description'],
            'meta_keyword' => $requestData['meta_keyword'],
            'meta_description' => $requestData['meta_description'],
        ]);
    }
}
