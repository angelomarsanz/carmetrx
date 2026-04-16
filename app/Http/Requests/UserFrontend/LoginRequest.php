<?php

namespace App\Http\Requests\UserFrontend;

use App\Models\User;
use App\Models\User\BasicSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class LoginRequest extends FormRequest
{
    private $recaptchaStatus;
    public function __construct()
    {
        $user = getUser();
        $this->recaptchaStatus = BasicSetting::where('user_id', $user->id)
            ->value('google_recaptcha_status');
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
       
        return [
            'username' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => ($this->recaptchaStatus == 1) ? 'required|captcha' : ''
        ];
    }
    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {


        if ($this->recaptchaStatus == 1) {
            return [
                'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
                'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.'
            ];
        } else {
            return [];
        }
    }
}
