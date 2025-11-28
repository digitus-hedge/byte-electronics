<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $rule_name_unique = Rule::unique('categories', 'name')->whereNull('deleted_at');
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rule_name_unique->ignore($this->category->id);
        }
        $rule_slug_unique = Rule::unique('categories', 'slug');
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rule_slug_unique->ignore($this->category->id);
        }
        return [
            'name' => ['required', $rule_name_unique],
            'slug' => ['required', $rule_slug_unique
//            "regex:/^[0-9a-z.\s,'_]*$/"
//                'alpha_dash'
                ],
            'description' => ['nullable'],
            'image' => ['nullable','dimensions:min_width=109,min_height=79,max_width=500,max_height=500'],
            'meta_description' => ['nullable'],
            'meta_tag' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'slug.small_no_spaces' => 'must be small cases and without spaces',
//            'body.required'  => 'A message is required',
        ];
    }
}
