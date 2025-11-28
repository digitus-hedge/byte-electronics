<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;  

class ProductRequest extends FormRequest
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
        Log::info('ProductRequest Rules:', $this->all());
        $rule_name_unique = Rule::unique('products', 'name')->where(function ($query) {
            $query->whereNull('deleted_at');
        });
        $rule_slug_unique = Rule::unique('products', 'slug')->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rule_name_unique->ignore($this->product->id);
            $rule_slug_unique->ignore($this->product->id);
        }
    
        return [
            'name' => ['required', 'string', 'max:255', $rule_name_unique],
            'slug' => ['required', 'string', 'max:255', $rule_slug_unique],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sub_category_id' => ['required', 'integer', 'exists:sub_categories,id'],
           'file_name' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'org_name' => ['nullable', 'string', 'max:255'],
            'manufacturers_no' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'more_description' => ['nullable', 'string'],
            'tag' => ['required', 'array'],
            'tag.*' => ['string'],
            //'meta_tag' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'minimum_qty' => ['required', 'integer', 'min:1'],
            'is_repairable' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ];
    }

   

    public function messages()
    {
        return [
            'product_id.unique' => 'Product id already exist with same brand , category and subcategory.',
            'slug.unique' => 'Product slug already exist with same brand , category and subcategory.',
            'slug.small_no_spaces' => 'must be small cases and without special chars except underscore',
        ];
    }
}
