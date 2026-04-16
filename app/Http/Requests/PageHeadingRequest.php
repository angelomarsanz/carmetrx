<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageHeadingRequest extends FormRequest
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
      'about_page_title' => 'required',
      'blog_page_title' => 'required',
      'contact_page_title' => 'required',
      'agent_login_page_title' => 'required',
      'projects_page_title' => 'required',
      'properties_page_title' => 'required',
      'faq_page_title' => 'required',
      'forget_password_page_title' => 'required',
      'login_page_title' => 'required',
      'signup_page_title' => 'required',
      'agents_page_title' => 'required'
    ];
  }
}
