<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class SubCategoryRequest extends FormRequest
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
    public function rules(Request $request)
    {  
        $rule_name_unique = Rule::unique('sub_categories', 'name')->where('category_id',$request->category_id)->whereNull('deleted_at');
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rule_name_unique->ignore($this->subcategory->id);
        }
        $rule_slug_unique = Rule::unique('sub_categories', 'slug')->where('category_id',$request->category_id);
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rule_slug_unique->ignore($this->subcategory->id);
        }
        return [
            'name' => ['required', $rule_name_unique],
            'slug' => ['required', $rule_slug_unique],
            'category_id' => ['required'],
            'parent_id' => ['nullable'],
            'description' => ['nullable'],
            'image_sub_cat' => ['nullable','dimensions:min_width=109,min_height=79,max_width=111,max_height=81'],
            'product_default_sub_cat' => ['nullable','dimensions:min_width=799,min_height=799,min_width=801,max_height=801'],
            'meta_tag' => ['nullable'],
            'meta_description' => ['nullable'],
            'status' => ['required'],
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
