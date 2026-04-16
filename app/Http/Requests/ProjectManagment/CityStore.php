<?php

namespace App\Http\Requests\ProjectManagment;

use App\Models\User\BasicSetting;
use App\Models\User\Project\Country;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CityStore extends FormRequest
{
    use TenantFrontendLanguage;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenantId = Auth::guard('web')->user()->id;
        $country =  $this->country;
        $rules = [
            'state' => [
                Rule::requiredIf(function () use ($country, $tenantId) {
                    if ($country) {
                        $country = Country::where('user_id', $tenantId)->findOrFail($country);
                        if (count($country->states) > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    return false;
                })
            ],
            'image' => "required|mimes:jpg,png,svg,jpeg,webp",
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];

        $basicSettings = BasicSetting::where('user_id', $tenantId)->select('project_state_status', 'project_country_status')->first();
        if ($basicSettings->project_country_status == 1) {
            $rules['country'] = 'required';
        }
        $languages = $this->allLangs($tenantId);
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_project_city_contents', 'name')->where('user_id', $tenantId)

                ];
        }

        return $rules;
    }
    public function messages()
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        foreach ($languages as $lan) {

            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' '  . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }
        return $message;
    }
}
