<?php

namespace App\Http\Requests\ProjectManagment;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Auth;

class ProjectTypeRequest extends FormRequest
{
    use TenantFrontendLanguage;
    public $tenantId;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        if (Auth::guard('web')->check()) {
            $this->tenantId = Auth::guard('web')->user()->id;
            return true;
        }

        if (Auth::guard('agent')->check()) {
            $this->tenantId = Auth::guard('agent')->user()->user_id;
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $languages = $this->allLangs($this->tenantId);
        $defaulLang = $this->defaultLang($this->tenantId);
        $rules = [];

        $rules[$defaulLang->code . '_name'] = 'required|max:255';
        $rules[$defaulLang->code . '_total_unit'] = 'required|numeric';
        $rules[$defaulLang->code . '_min_price'] = 'required|numeric';
        $rules[$defaulLang->code . '_min_area'] = 'required|numeric';

        $rules[$defaulLang->code . '_max_area'] = 'nullable|numeric';
        $rules[$defaulLang->code . '_max_price'] = 'nullable|numeric';


        foreach ($languages as $language) {
            if ($language->id == $defaulLang->id) {
                continue;
            }
            if ($this->filled($language->code . '_name') || $this->filled($language->code . '_total_unit') || $this->filled($language->code . '_min_price') || $this->filled($language->code . '_min_area') || $this->filled($language->code . '_max_area') || $this->filled($language->code . '_max_price')) {
                $rules[$language->code . '_name'] = 'required|max:255';
                $rules[$language->code . '_total_unit'] = 'required|numeric';
                $rules[$language->code . '_min_price'] = 'required|numeric';
                $rules[$language->code . '_min_area'] = 'required|numeric';

                $rules[$language->code . '_max_area'] = 'nullable|numeric';
                $rules[$language->code . '_max_price'] = 'nullable|numeric';
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
        $messages = [];


        $languages = $this->allLangs($this->tenantId);

        foreach ($languages as $language) {

            $messages[$language->code . '_name.required'] = __("The name field is required for") . ' ' . $language->name . ' ' . __("language");

            $messages[$language->code . '_total_unit.required'] = __("The total unit field is required for") . ' ' . $language->name . ' ' . __("language");
            $messages[$language->code . '_total_unit.numeric'] = __("The total unit field must be a number for") . ' ' . $language->name . ' ' . __("language");

            $messages[$language->code . '_min_price.required'] = __("The min price field is required for") . ' ' . $language->name . ' ' . __("language");
            $messages[$language->code . '_min_price.numeric'] = __("The min price field must be a number for") . ' ' . $language->name . ' ' . __("language");

            $messages[$language->code . '_min_area.required'] = __("The min area field is required for") . ' ' . $language->name . ' ' . __("language");
            $messages[$language->code . '_min_area.numeric'] = __("The min area field must be a number for") . ' ' . $language->name . ' ' . __("language");

            $messages[$language->code . '_max_area.numeric'] = __("The max area field must be a number for") . ' ' . $language->name . ' ' . __("language");

            $messages[$language->code . '_max_price.numeric'] = __("The max price field must be a number for") . ' ' . $language->name . ' ' . __("language");
        }



        return $messages;
    }
}
