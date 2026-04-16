<?php

namespace App\Http\Requests\Testimonial;

use App\Rules\ImageMimeTypeRule;
use App\Traits\UserLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
{
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
   * @return array
   */
  public function rules()
  {
    return [

      'image' => [
        'required',
        new ImageMimeTypeRule()
      ],
      'name' => 'required|max:255',
      'occupation' => 'required|max:255',
      'comment' => 'required',

    ];
  }
}
