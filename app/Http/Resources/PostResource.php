<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content'      => $this->content,
            'image'        => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'status'       => $this->status,
            'is_featured'  => (bool) $this->is_featured,
            'reading_time' => $this->reading_time,
            'views'        => $this->views_count,
            'likes'        => $this->likes_count,
            'published_at' => $this->published_at?->toIso8601String(),
            'category'     => new CategoryResource($this->whenLoaded('category')),
            'author'       => [
                'name'        => $this->author?->name,
                'designation' => $this->author?->designation,
                'avatar'      => $this->author?->avatar ? asset('storage/' . $this->author->avatar) : null,
            ],
            'tags'         => TagResource::collection($this->whenLoaded('tags')),
            'seo'          => [
                'meta_title'       => $this->meta_title ?: $this->title,
                'meta_description' => $this->meta_description ?: $this->excerpt,
                'score'            => $this->seo_score,
            ],
            'links'        => [
                'self'     => route('blog.show', $this->slug),
                'api_self' => url('/api/v1/posts/' . $this->slug),
            ],
        ];
    }
}
