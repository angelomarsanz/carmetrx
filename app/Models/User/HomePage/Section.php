<?php

namespace App\Models\User\HomePage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Section extends Model
{
  use HasFactory;

  public $table = "user_sections";
  protected $fillable = [
    'user_id',
    'work_steps_section_status',
    'category_section_status',
    'featured_properties_section_status',
    'counter_section_status',
    'cities_section_status',
    'testimonial_section_status',
    'video_section_status',
    'about_section_status',
    'newsletter_section_status',
    'why_choose_us_section_status',
    'agent_section_status',
    'pricing_section_status',
    'partner_section_status',
    'project_section_status',
    'property_section_status',
    'footer_section_status',
  ];

  public function updateOrCreateSection($tenantId, array $data = [])
  {
    $defaultData = [
      'work_steps_section_status' => 1,
      'category_section_status' => 1,
      'featured_properties_section_status' => 1,
      'counter_section_status' => 1,
      'cities_section_status' => 1,
      'testimonial_section_status' => 1,
      'video_section_status' => 1,
      'about_section_status' => 1,
      'newsletter_section_status' => 1,
      'why_choose_us_section_status' => 1,
      'agent_section_status' => 1,
      'pricing_section_status' => 1,
      'partner_section_status' => 1,
      'project_section_status' => 1,
      'property_section_status' => 1,
      'footer_section_status' => 1,
    ];
    $mergedData = array_merge($defaultData, $data);

    return self::updateOrcreate(
      ['user_id' => $tenantId],
      $mergedData
    );
  }
}
