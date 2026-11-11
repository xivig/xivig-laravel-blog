<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model {
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'tagline',
        'description',
        'icon',
        'badge',
        'cover_image',
        'pricing_type',
        'price',
        'price_unit',
        'delivery_time',
        'features',
        'target_audience',
        'is_featured',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'features'    => 'array',
        'is_featured' => 'boolean',
        'price'       => 'decimal:2',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    public function inquiries(): HasMany {
        return $this->hasMany(ServiceInquiry::class);
    }

    public function scopeActive($query) {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', true);
    }

    public function getFormattedPriceAttribute(): string {
        if ($this->pricing_type === 'quote') {
            return 'Custom Quote';
        }

        if (empty($this->price) || $this->price == 0) {
            return 'Complimentary';
        }

        $formatted = '$' . number_format($this->price, 0);

        if ($this->pricing_type === 'starting_at') {
            $formatted = 'From ' . $formatted;
        } elseif ($this->pricing_type === 'hourly') {
            $formatted .= ' / hr';
        } elseif ($this->pricing_type === 'retainer') {
            $formatted .= ' / mo';
        }

        if (!empty($this->price_unit) && !in_array($this->pricing_type, ['hourly', 'retainer'])) {
            $formatted .= ' ' . $this->price_unit;
        }

        return $formatted;
    }

    public function getFeaturesListAttribute(): array {
        if (is_array($this->features)) {
            return array_filter($this->features);
        }

        if (is_string($this->features)) {
            $decoded = json_decode($this->features, true);
            if (is_array($decoded)) {
                return array_filter($decoded);
            }
            return array_filter(array_map('trim', explode("\n", $this->features)));
        }

        return [];
    }

    public function getStatusBadgeAttribute(): array {
        return match ($this->status) {
            'active'   => ['label' => 'Active', 'class' => 'bg-success-subtle text-success border border-success-subtle'],
            'draft'    => ['label' => 'Draft', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'],
            'archived' => ['label' => 'Archived', 'class' => 'bg-dark-subtle text-muted border border-dark-subtle'],
            default    => ['label' => ucfirst($this->status), 'class' => 'bg-light text-muted border'],
        };
    }
}
