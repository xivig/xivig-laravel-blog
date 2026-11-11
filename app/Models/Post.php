<?php

namespace App\Models;

use App\Services\SeoService;
use App\Traits\Filterable;
use App\Traits\HasSeo;
use App\Traits\HasSlug;
use App\Traits\TrackActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class Post extends Model {
    use HasFactory, SoftDeletes, HasSlug, HasSeo, TrackActivity, Filterable;

    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_caption',
        'featured_image_alt',
        'status',
        'is_featured',
        'is_editor_pick',
        'is_sticky',
        'is_private',
        'password',
        'views_count',
        'likes_count',
        'reading_time',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_editor_pick' => 'boolean',
        'is_sticky' => 'boolean',
        'is_private' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'reading_time' => 'integer',
    ];

    public function author() {
        return $this->belongsTo(Author::class);
    }

    public function getImageAttribute(): ?object {
        if (empty($this->featured_image)) {
            return null;
        }
        $link = str_starts_with($this->featured_image, 'http')
            ? $this->featured_image
            : (str_starts_with($this->featured_image, 'storage/') ? $this->featured_image : 'storage/' . ltrim($this->featured_image, '/'));
        return (object) ['link' => $link];
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments() {
        return $this->hasMany(Comment::class)->where('status', 'approved')->whereNull('parent_id')->with('replies');
    }

    public function scopePublished($query) {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query) {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query) {
        return $query->where('status', 'scheduled');
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', true);
    }

    public function scopeEditorPick($query) {
        return $query->where('is_editor_pick', true);
    }

    public function scopeSticky($query) {
        return $query->where('is_sticky', true);
    }

    public function scopePublicVisible($query) {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where('is_private', false);
    }

    public function getFeaturedImageAltAttribute(): string {
        return (string) ($this->attributes['featured_image_alt'] ?? $this->featured_image_caption ?? $this->title);
    }

    public function galleryImages(): HasMany {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    public function isPasswordProtected(): bool {
        return !empty($this->password);
    }

    public function setPasswordAttribute($value): void {
        if (empty($value)) {
            $this->attributes['password'] = null;
        } elseif (str_starts_with((string) $value, '$2y$') || str_starts_with((string) $value, '$2a$')) {
            $this->attributes['password'] = $value;
        } else {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function checkPassword(string $input): bool {
        if (!$this->isPasswordProtected()) return true;

        try {
            return Hash::check($input, (string) $this->password);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function isUnlocked(Request $request): bool {
        if (!$this->isPasswordProtected()) return true;
        if (session('unlocked_post_' . $this->id) === true) return true;
        if ($request->user() && $request->user()->can('view_posts')) return true;
        return false;
    }

    public function incrementViews() {
        $this->increment('views_count');
    }

    public function incrementLikes() {
        $this->increment('likes_count');
    }

    public function revisions(): MorphMany {
        return $this->morphMany(Revision::class, 'revisionable')->latest();
    }

    public function pageViews(): MorphMany {
        return $this->morphMany(PageView::class, 'viewable');
    }

    /**
     * Get computed SEO Health Score (0-100) and criteria.
     */
    public function getSeoAuditAttribute(): array {
        return SeoService::auditPost($this);
    }

    /**
     * Get computed SEO Score integer.
     */
    public function getSeoScoreAttribute(): int {
        return $this->seo_audit['score'] ?? 0;
    }
}
