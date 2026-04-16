<?php

namespace App\Http\Requests\Blog;

use App\Models\User\CustomPage\PageContent;
use App\Models\User\Journal\BlogInformation;
use App\Models\User\Language;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
  public function rules()
  {
    $ruleArray = [
      'image' => $this->hasFile('image') ? new ImageMimeTypeRule() : '',
      'serial_number' => 'required|numeric',
      'category_id' => 'required|numeric',
    ];
    $tenantId = Auth::guard('web')->user()->id;
    $languages = $this->allLangs($tenantId);
    $id = $this->route('id');
    $request = $this->request->all();

    $defaulLang = $this->defaultLang($tenantId);

    $ruleArray[$defaulLang->code . '_title'] = 'required|max:255';
    $ruleArray[$defaulLang->code . '_author'] = 'required';
    $ruleArray[$defaulLang->code . '_content'] = 'required';

    foreach ($languages as $language) {
      // Skip the default language as it's always required
      if ($language->id == $defaulLang->id) {
        continue;
      }

      if ($this->filled($language->code . '_title') || $this->filled($language->code . '_author') || $this->filled($language->code . '_content') || $this->filled($language->code . '_meta_keywords') || $this->filled($language->code . '_meta_description')) {
        $slug = slug_create($request[$language->code . '_title']);
        $ruleArray[$language->code . '_title'] = [
          'required',
          'max:255',
          function ($attribute, $value, $fail) use ($slug, $id, $language) {
            $bis = BlogInformation::where('blog_id', '<>', $id)->where('language_id', $language->id)->where('user_id', Auth::guard('web')->user()->id)->get();
            foreach ($bis as $key => $bi) {
              if (strtolower($slug) == strtolower($bi->slug)) {
                $fail(__('The title field must be unique for') . ' ' . $language->name . ' ' . __('language'));
              }
            }
          }
        ];
        // $ruleArray[$language->code . '_category_id'] = 'required';
        $ruleArray[$language->code . '_author'] = 'required';
        $ruleArray[$language->code . '_content'] = 'required';
      }
    }

    return $ruleArray;
  }

  public function messages(): array
  {
    $messageArray = [];

    $languages = Language::query()->where('user_id', Auth::guard('web')->user()->id)->get();

    foreach ($languages as $language) {


      $messageArray[$language->code . '_title.required'] = __('The title field is required for') . ' ' . $language->name . __('language');

      $messageArray[$language->code . '_title.max'] = __('The title field cannot contain more than 255 characters for') . ' ' . $language->name . ' ' . __('language');

      $messageArray[$language->code . '_title.unique'] = __('The title field must be unique for') . ' ' . $language->name . ' language.';

      $messageArray[$language->code . '_author.required'] = __('The author field is required for') . ' ' . $language->name . ' ' . __('language');

      $messageArray[$language->code . '_content.required'] = __('The content field is required  for') . $language->name . ' ' . __('language');
    }
    $messageArray['category_id.required'] = __('The category field is required');
    return $messageArray;
  }
}
