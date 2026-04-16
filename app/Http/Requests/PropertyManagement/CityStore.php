<?php

namespace App\Http\Requests\PropertyManagement;

use App\Traits\AdminLanguage;
use Illuminate\Validation\Rule;
use App\Models\User\BasicSetting;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Property\Country;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;

class CityStore extends FormRequest
{
    use AdminLanguage;
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
        $tenantId = 0;
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


        $rules['country'] = 'required';
        $languages = $this->allLangs();
        foreach ($languages as $lan) {
            $rules[$lan->code . '_name'] =
                [
                    'required',
                    Rule::unique('user_city_contents', 'name')->where('user_id', $tenantId)

                ];
        }

        return $rules;
    }
    public function messages()
    {
        $message = [];
        $languages = $this->allLangs();
        $message['country.required'] = __('The state field is required.');
        $message['state.required'] = __('The city field is required.');
        foreach ($languages as $lan) {

            $message[$lan->code . '_name.required'] = __('The name field is required for') . ' ' . $lan->name . ' '  . __('language');
            $message[$lan->code . '_name.unique'] = __('The name field must be unique for') . ' ' . $lan->name . ' ' . __('language');
        }
        return $message;
    }
}
