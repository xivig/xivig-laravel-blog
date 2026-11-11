<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'content'      => $this->content,
            'template'     => $this->template,
            'is_published' => (bool) $this->is_published,
            'seo'          => [
                'meta_title'       => $this->meta_title ?: $this->title,
                'meta_description' => $this->meta_description,
                'meta_keywords'    => $this->meta_keywords,
            ],
            'links'        => [
                'self' => route('page.show', $this->slug),
            ],
        ];
    }
}
