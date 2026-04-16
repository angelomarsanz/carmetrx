<?php

namespace App\Http\Requests;

use App\Models\User\Journal\BlogCategory;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class BlogCategoryRequest extends FormRequest
{
  use TenantFrontendLanguage;
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    $tenantId = Auth::guard('web')->user()->id;
    $rules = [

      

      'status' => 'required|numeric',
      'serial_number' => 'required|numeric'
    ];

    $languages = $this->allLangs($tenantId);
    foreach ($languages as $lan) {
      $rules[$lan->code . '_name'] =
        [
          'required',
          Rule::unique('user_blog_category_contents', 'name')->where('user_id', $tenantId)

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
