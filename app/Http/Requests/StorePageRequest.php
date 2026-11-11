<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'title'            => 'required|string|max:200',
            'slug'             => 'nullable|string|max:200|unique:pages,slug',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:300',
            'template'         => 'nullable|string',
            'is_published'     => 'boolean',
        ];
    }
}
