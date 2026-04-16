<?php

namespace App\Http\Requests\Blog;

use App\Models\User\Journal\BlogInformation;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
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
    $ruleArray = [
      'image' => [
        'required',
        new ImageMimeTypeRule()
      ],
      'category_id' => 'required|numeric',
      'serial_number' => 'required|numeric'
    ];
    $tenantId = Auth::guard('web')->user()->id;
    $languages = $this->allLangs($tenantId);
    $defaulLang = $this->defaultLang($tenantId);
    $ruleArray[$defaulLang->code . '_title'] = 'required|max:255';
    $ruleArray[$defaulLang->code . '_author'] = 'required';
    $ruleArray[$defaulLang->code . '_content'] = 'required';

    foreach ($languages as $language) {
      if ($language->id == $defaulLang->id) {
        continue;
      }
      if ($this->filled($language->code . '_title') || $this->filled($language->code . '_author') || $this->filled($language->code . '_content') || $this->filled($language->code . '_meta_keywords') || $this->filled($language->code . '_meta_description')) {

        $request = $this->request->all();
        $slug = slug_create($request[$language->code . '_title']);
        $ruleArray[$language->code . '_title'] = [
          'required',
          'max:255',
          function ($attribute, $value, $fail) use ($slug, $language, $tenantId) {
            $bis = BlogInformation::where('language_id', $language->id)->where('user_id', $tenantId)->get();
            foreach ($bis as $key => $bi) {
              if (strtolower($slug) == strtolower($bi->slug)) {
                $fail(__('The title field must be unique for') . ' ' . $language->name . ' ' . __('language'));
              }
            }
          }
        ];
        $ruleArray[$language->code . '_author'] = 'required';
        $ruleArray[$language->code . '_content'] = 'required';
      }
    }

    return $ruleArray;
  }

  public function messages(): array
  {
    $messageArray = [];

    $tenantId = Auth::guard('web')->user()->id;
    $languages = $this->allLangs($tenantId);

    foreach ($languages as $language) {
      $messageArray[$language->code . '_title.required'] = __('The title field is required for') . ' ' . $language->name . __('language');

      $messageArray[$language->code . '_title.max'] = __('The title field cannot contain more than 255 characters for') . ' ' . $language->name . ' ' . __('language');

      $messageArray[$language->code . '_title.unique'] = __('The title field must be unique for') . ' ' . $language->name . ' language.';

      $messageArray[$language->code . '_author.required'] = __('The author field is required for') . ' ' . $language->name . ' ' . __('language');

      $messageArray[$language->code . '_content.required'] = __('The content field is required for') . $language->name . ' ' . __('language');
    }
    $messageArray['category_id.required'] = __('The category field is required');
    return $messageArray;
  }
}
