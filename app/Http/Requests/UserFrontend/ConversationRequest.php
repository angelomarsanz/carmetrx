<?php

namespace App\Http\Requests\UserFrontend;

use Illuminate\Foundation\Http\FormRequest;

class ConversationRequest extends FormRequest
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
        $ruleArray = [
            'reply' => 'min:2'
        ];

        if ($this->hasFile('attachment')) {
            $file = $this->file('attachment');
            $fileExtension = $file->getClientOriginalExtension();

            // convert mb to kb
            $maxSize = 5 * 1024;

            $ruleArray['attachment'] = [
                function ($attribute, $value, $fail) use ($fileExtension) {
                    if (strcmp('zip', $fileExtension) != 0) {
                        $fail('The ' . $attribute . ' must be a file of type: zip.');
                    }
                },
                'max:' . $maxSize
            ];
        }

        return $ruleArray;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messageArray = [
            'reply.min' => 'The reply field is required.'
        ];

        if ($this->hasFile('attachment')) {
            $messageArray['attachment.max'] = 'The file must not be greater than 5 megabytes.';
        }

        return $messageArray;
    }
}
