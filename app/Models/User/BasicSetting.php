<?php

namespace App\Models\User;

use App\Constants\Constant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BasicSetting extends Model
{
    public $table = "user_basic_settings";

    protected $fillable = [
        'favicon',
        'logo',
        'cv',
        'email',
        'from_name',
        'website_title',
        'breadcrumb',
        'footer_logo',
        'footer_bg_img',
        'primary_color',
        'secondary_color',
        'theme_version',
        'user_id',
        'preloader',
        'preloader_status',
        'whatsapp_status',
        'whatsapp_number',
        'whatsapp_header_title',
        'whatsapp_popup_status',
        'whatsapp_popup_message',
        'disqus_status',
        'disqus_short_name',
        'storage_usage',
        'maintenance_img',
        'maintenance_msg',
        'maintenance_status',
        'bypass_token',
        'hero_bg_img',
        'hero_static_img',
        'google_recaptcha_status',
        'google_recaptcha_site_key',
        'google_recaptcha_secret_key',
        'google_login_status',
        'google_client_id',
        'google_client_secret',
        'base_currency_symbol',
        'base_currency_symbol_position',
        'base_currency_text',
        'base_currency_text_position',
        'base_currency_rate',
        'google_map_api_status',
        'google_map_api_key',
        'google_map_radius',
        'google_map_type',
        'ai_api_key',
        'ai_api_model',
        'ai_api_token',
        'ai_api_temperature',
    ];

    public function language(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Language::class, 'user_id');
    }

    public function logo(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>  !is_null($value) ?  Constant::WEBSITE_LOGO . '/' . $value : null
        );
    }
    public function preloader(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>  !is_null($value) ?  Constant::WEBSITE_PRELOADER . '/' . $value : null
        );
    }
    public function favicon(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>   !is_null($value) ? Constant::WEBSITE_FAVICON . '/' . $value : null
        );
    }
    protected function isCountryActive(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                $county_status = $this->basicInfo->property_country_status; // Change this to match your attribute name

                $attributeName = 'country_id';
                // Check if the attribute exists and is not null
                if ($this->attributes[$attributeName] && $county_status == 1) {

                    return true;
                }

                return false; // Return false if the attribute is null or doesn't exist
            }
        );
    }

    public function deleteBasicsetting()
    {
        DB::transaction(function () {
            $basicSetting = $this;
            // ===== favicon image delete =====
            @unlink(public_path(Constant::WEBSITE_FAVICON . '/' . $basicSetting->favicon));
            // ===== logo image delete =====
            @unlink(public_path(Constant::WEBSITE_LOGO . '/' . $basicSetting->logo));
            // ===== preloader image delete =====
            @unlink(public_path(Constant::WEBSITE_PRELOADER . '/' . $basicSetting->preloader));
            // ===== breadcrumb image delete =====
            @unlink(public_path(Constant::WEBSITE_BREADCRUMB . '/' . $basicSetting->breadcrumb));
            // ===== footer logo image delete =====
            @unlink(public_path(Constant::WEBSITE_FOOTER_LOGO . '/' . $basicSetting->footer_logo));
            // ===== footer logo image delete =====
            @unlink(public_path(Constant::WEBSITE_FOOTER_LOGO . '/' . $basicSetting->footer_bg_img));
            // ===== maintenance  image delete =====
            @unlink(public_path(Constant::WEBSITE_MAINTENANCE_IMAGE . '/' . $basicSetting->maintenance_img));

            $basicSetting->delete();
        });
    }
}
