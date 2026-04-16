<?php

namespace App\Http\Requests\Agent;

use App\Rules\ImageMimeTypeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'image' => $this->hasFile('image') ? new ImageMimeTypeRule() : '',

            'username' => [
                'required',
                'max:255',
                Rule::unique('user_agents')->where('user_id', Auth::guard('web')->user()->id)->ignore($this->id),
                'regex:/^\S*$/u'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('user_agents')->where('user_id', Auth::guard('web')->user()->id)->ignore($this->id)
            ],
            'password' => 'required|min:6|confirmed',
        ];
    }
    public function messages()
    {
        return [
            'username.regex' => 'Space are not allowed in the username field.',
        ];
    }
}
