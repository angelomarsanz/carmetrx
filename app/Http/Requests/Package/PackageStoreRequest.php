<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class PackageStoreRequest extends FormRequest
{
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
        $features = request()->has('features') && is_array(request('features')) ? request('features') : [];
        return [
            'title' => 'required|max:255',
            'price' => 'required',
            "status" => 'required',
            'term' => 'required',
            'features' => 'required|array',
            // 'user_limit' => in_array('User', $this->features) ? 'required|numeric' : '',
            'number_of_additional_page' => in_array('Additional Page', $features) ? 'required|numeric' : '',
            'number_of_blog_post' => in_array('Blog', $features) ? 'required|numeric' : '',
            'number_of_language' => in_array('Language', $features) ? 'required|numeric' : '',
            'number_of_agent' => in_array('Agent', $features) ? 'required|numeric' : '',
            'number_of_property' => in_array('Property Management', $features) ? 'required|numeric' : '',
            'number_of_property_featured' => in_array('Property Management', $features) ? 'required|numeric' : '',
            'number_of_property_gallery_images' => in_array('Property Management', $features) ? 'required|numeric' : '',
            'number_of_property_additional_features' => in_array('Property Management', $features) ? 'required|numeric' : '',

            'number_of_projects' => in_array('Project Management', $features) ? 'required|numeric' : '',
            'number_of_project_types' => in_array('Project Management', $features) ? 'required|numeric' : '',
            'number_of_project_gallery_images' => in_array('Project Management', $features) ? 'required|numeric' : '',
            'number_of_project_additional_features' => in_array('Project Management', $features) ? 'required|numeric' : '',

            'trial_days' => $this->is_trial == "1" ? 'required' : '',
        ];
    }
    public function messages(): array
    {
        return [
            // 'user_limit.required' => 'Number of user is required when user option is checked.',

            // 'number_of_agent.required' => 'The number of agents field is required.',
            'number_of_property_featured.required' => __('The number of featured propeties field is required.'),
            // 'number_of_property_adittionl_specifications.required' => 'The number of property additionl features field is required.',
            // 'number_of_project_additionl_specifications.required' => 'The number of project additionl features field is required.',

            // 'trial_days.required' => 'Trial days is required when trial option is checked.',
        ];
    }
}
