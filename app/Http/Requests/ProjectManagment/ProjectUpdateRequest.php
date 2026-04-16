<?php

namespace App\Http\Requests\ProjectManagment;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\BasicSetting;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProjectUpdateRequest extends FormRequest
{
    use TenantFrontendLanguage;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $userId = Auth::guard('web')->user()->id;

        if (Auth::guard('agent')->check() && Auth::guard('agent')->user()) {
            $userId = Auth::guard('agent')->user()->user_id;
        } elseif (Auth::guard('web')->check() && Auth::guard('web')->user()) {
            $userId = Auth::guard('web')->user()->id;
        }
        $userCurrentPackage =  UserPermissionHelper::currentPackage($userId);


        $rules = [
            'featured_image' => [
                new ImageMimeTypeRule()
            ],
            'category_id' => 'required',
            'city_id' => 'required',
            'min_price' => 'required|numeric',
            'status' => 'required',
            'latitude' => ['required', 'numeric', 'regex:/^[-]?((([0-8]?[0-9])\.(\d+))|(90(\.0+)?))$/'],
            'longitude' => ['required', 'numeric', 'regex:/^[-]?((([1]?[0-7]?[0-9])\.(\d+))|([0-9]?[0-9])\.(\d+)|(180(\.0+)?))$/']

        ];
        $basicSettings = BasicSetting::where('user_id', $userId)->select('project_state_status', 'project_country_status')->first();
        if ($basicSettings->project_country_status == 1) {
            $rules['country_id'] = 'required';
        }
        if ($basicSettings->project_state_status == 1) {
            $rules['state_id'] =  'required';
        }
        $languages = $this->allLangs($userId);
        $defaulLang = $this->defaultLang($userId);

        $rules[$defaulLang->code . '_title'] = 'required|max:255';
        $rules[$defaulLang->code . '_address'] = 'required';
        $rules[$defaulLang->code . '_description'] = 'required';

        foreach ($languages as $language) {
            // Skip the default language as it's always required
            if ($language->id == $defaulLang->id) {
                continue;
            }

            if ($this->filled($language->code . '_title') || $this->filled($language->code . '_address') || $this->filled($language->code . '_description') || $this->filled($language->code . '_meta_keyword') || $this->filled($language->code . '_meta_description')) {
                $rules[$language->code . '_title'] = 'required|max:255';
                $rules[$language->code . '_address'] = 'required';
                // $rules[$language->code . '_amenities'] = 'required';
                $rules[$language->code . '_description'] = 'required';

                $rules[$language->code . '_label'] = 'array|max:' .  $userCurrentPackage->number_of_project_additionl_specifications;
            }
        }

        return $rules;
    }
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $message = [];
        if (Auth::guard('agent')->check() && Auth::guard('agent')->user()) {
            $userId = Auth::guard('agent')->user()->user_id;
        } elseif (Auth::guard('web')->check() && Auth::guard('web')->user()) {
            $userId = Auth::guard('web')->user()->id;
        }
        $languages = $this->allLangs($userId);

        foreach ($languages as $language) {


            $message[$language->code . '_title.required'] = __('The title field is required for') . ' ' . $language->name . ' ' . __('language');
            $message[$language->code . '_address.required'] = __('The address field is required for') . ' ' . $language->name . ' ' . __('language');
            $message[$language->code . '_description.required'] = __('The description field is required for') . ' ' . $language->name . ' ' . __('language');
            $message[$language->code . '_description.min'] = __('The description  must be at least 15 characters for') . $language->name . ' ' . __('language');
            $message[$language->code . '_label.max'] = __('Additional Features shall not exceed') . ' :max ' . $language->name . ' ' . __('language');
        }

        $message['category_id.required'] = __('The category field is required');
        $message['country_id.required'] = __('The country field is required');
        $message['state_id.required'] = __('The state field is required');
        $message['city_id.required'] = __('The city field is required');
        return $message;
    }
}
