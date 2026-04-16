<?php

namespace App\Models\User\HomePage;

use App\Models\User\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionTitle extends Model
{
    use HasFactory;

    public $table = 'user_section_titles';

    protected $fillable = [
        'user_id',
        'language_id',
        'category_section_title',
        'category_section_subtitle',
        'property_section_title',
        'project_section_title',
        'project_section_subtitle',
        'featured_property_section_title',
        'city_section_title',
        'city_section_subtitle',
        'agent_section_title',
        'agent_section_subtitle',
        'work_process_title',
        'work_process_subtitle'
    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function updateOrCreateSectionTitle($tenantId, $languageId, array $data = [])
    {
        $defaultData = [
            'category_section_title' => null,
            'category_section_subtitle' => null,
            'property_section_title' => null,
            'project_section_title' => null,
            'project_section_subtitle' => null,
            'featured_property_section_title' => null,
            'city_section_title' => null,
            'city_section_subtitle' => null,
            'agent_section_title' => null,
            'agent_section_subtitle' => null,
            'work_process_title' => null,
            'work_process_subtitle' => null
        ];
        $mergedData = array_merge($defaultData, $data);

        return self::updateOrcreate(
            [
                'user_id' => $tenantId,
                'language_id' => $languageId
            ],
            $mergedData
        );
    }
}
