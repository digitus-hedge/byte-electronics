<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BrandRequest extends FormRequest
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
        $brandId = $this->route('brand') ? $this->route('brand')->id : null;
        
        $rule_name_unique = Rule::unique('brands', 'name')->ignore($brandId);
        if (in_array($this->method(), ['PUT', 'PATCH']) && isset($this->brand)) {
            $rule_name_unique->ignore($this->brand->id);
        }

        $rule_slug_unique = Rule::unique('brands', 'slug')->ignore($brandId);
        if (in_array($this->method(), ['PUT', 'PATCH']) && isset($this->brand)) {
            $rule_slug_unique->ignore($this->brand->id);
        }

        $image_required = 'required';
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $image_required = 'nullable';
        }
        return [
            'name' => ['required', $rule_name_unique],
            'slug' => ['required', $rule_slug_unique],
            'description' => ['nullable'],
            'meta_tag' => 'required',
            'meta_description' => 'required',
            'image' => [$image_required],
            'image' => [$image_required, 'nullable', 'dimensions:min_width=109,min_height=79,max_width=111,max_height=81'],
            'secondary_logo' => [
                'nullable',
                'dimensions:min_width=109,min_height=79,max_width=150,max_height=110',
            ],
            'banner' => [
                'nullable',
                'dimensions:min_width=1349,min_height=270,max_width=1350,max_height=271',
            ],
        ];
    }
   
}
