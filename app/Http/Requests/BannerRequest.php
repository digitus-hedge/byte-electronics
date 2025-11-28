<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BannerRequest extends FormRequest
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
        $image_required= 'required';
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $image_required= 'nullable';
        }

        // if($this->banner_type == 1){
        //     $diamensions = 'dimensions:min_width=780,min_height=657,max_width=790,max_height=660';
        // }
        // else{
        //     $diamensions = 'dimensions:min_width=380,min_height=320,max_width=385,max_height=320';
        // }
        return [
            'redirect_url' => ['nullable','url'],
            'type' => ['required'],
            // 'image' => [$image_required,$diamensions],
            'image' => [$image_required],
            'priority' => ['required'],
        ];
    }
}
