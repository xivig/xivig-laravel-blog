<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'title'                  => 'required|string|max:255',
            'slug'                   => 'nullable|string|max:255|unique:posts,slug',
            'category_id'            => 'required|exists:categories,id',
            'author_id'              => 'nullable|exists:authors,id',
            'excerpt'                => 'nullable|string|max:1000',
            'content'                => 'required|string',
            'status'                 => 'required|in:draft,scheduled,published,private',
            'featured_image'         => 'nullable|image|max:10240',
            'featured_image_caption' => 'nullable|string|max:255',
            'is_featured'            => 'boolean',
            'is_editor_pick'         => 'boolean',
            'published_at'           => 'nullable|date',
            'meta_title'             => 'nullable|string|max:200',
            'meta_description'       => 'nullable|string|max:500',
            'meta_keywords'          => 'nullable|string|max:300',
            'canonical_url'          => 'nullable|url|max:300',
            'tags'                   => 'nullable|array',
            'tags.*'                 => 'exists:tags,id',
        ];
    }
}
