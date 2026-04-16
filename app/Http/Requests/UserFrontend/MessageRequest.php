<?php

namespace App\Http\Requests\UserFrontend;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
        $ruleArray = [];

        if (!$this->filled('msg') && !$this->hasFile('attachment')) {
            $ruleArray['msg'] = 'required';
        }

        if ($this->hasFile('attachment')) {
            $file = $this->file('attachment');
            $fileExtension = $file->getClientOriginalExtension();

            $allowedExtensions = array('jpg', 'jpeg', 'png', 'rar', 'zip', 'txt', 'doc', 'docx', 'pdf');

            $ruleArray['attachment'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $fail('The ' . $attribute . ' must be a file of type: jpg, jpeg, png, rar, zip, txt, doc, docx or pdf.');
                }
            };
        }

        return $ruleArray;
    }
}
