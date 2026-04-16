<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicSetting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'language_id',
        'intro_subtitle',
        'intro_title',
        'intro_text',
        'intro_main_image',
        'team_section_title',
        'team_section_subtitle',
        'hero_section',
        'template_section',
        'work_process_section',
        'featured_users_section',
        'pricing_section',
        'partners_section',
        'intro_section',
        'intro_section_button_text',
        'intro_section_button_url',
        'intro_section_video_url',
        'testimonial_section',
        'blog_section',
        'top_footer_section',
        'copyright_section',
        'footer_text',
        'copyright_text',
        'footer_logo',
        'maintainance_mode',
        'maintainance_text',
        'maintenance_img',
        'maintenance_status',
        'secret_path',
        'pricing_text',
        'support_ticket_status',
        'additional_section_status',
        'about_additional_section_status'
    ];

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }
}
