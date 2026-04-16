<?php

namespace App\Http\Requests\UserFrontend;

use App\Models\User\BasicSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class SignupRequest extends FormRequest
{
    public $user;
    public function __construct()
    {
        $this->user = getUser();
    }
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

        $user = $this->user;

        $recaptchaStatus = BasicSetting::where('user_id', $user->id)->pluck('google_recaptcha_status')->first();
        return [
            'username' => [
                'required',
                'max:255',
                Rule::unique('customers', 'username')->where(function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })

            ],
            'email_address' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('customers', 'email')->where(function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
            ],
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
            'g-recaptcha-response' => ($recaptchaStatus == 1) ? 'required|captcha' : ''
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $recaptchaStatus = BasicSetting::where('user_id', $this->user->id)->pluck('google_recaptcha_status')->first();

        return [
            'password_confirmation.required' => 'The confirm password field is required.',
            'g-recaptcha-response.required' => ($recaptchaStatus == 1) ? 'Please verify that you are not a robot.' : '',
            'g-recaptcha-response.captcha' => ($recaptchaStatus == 1) ? 'Captcha error! try again later or contact site admin.' : ''
        ];
    }
}
