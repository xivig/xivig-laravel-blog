<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model {
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'customer_name',
        'customer_email',
        'amount',
        'currency',
        'payment_method',
        'status',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            'JPY' => '¥',
        ];
        $sym = $symbols[strtoupper($this->currency ?? 'USD')] ?? ($this->currency . ' ');
        return $sym . number_format((float) $this->amount, 2);
    }

    public function getStatusBadgeClassAttribute(): string {
        return match (strtolower($this->status)) {
            'completed', 'paid', 'success' => 'bg-success-subtle text-success border border-success border-opacity-25',
            'pending', 'processing'        => 'bg-warning-subtle text-warning border border-warning border-opacity-25',
            'refunded'                    => 'bg-secondary-subtle text-secondary border border-secondary border-opacity-25',
            'failed', 'cancelled'          => 'bg-danger-subtle text-danger border border-danger border-opacity-25',
            default                       => 'bg-light text-muted border',
        };
    }

    public function getMethodIconAttribute(): string {
        $method = strtolower($this->payment_method ?? '');
        if (str_contains($method, 'stripe')) return 'bi bi-stripe';
        if (str_contains($method, 'paypal')) return 'bi bi-paypal';
        if (str_contains($method, 'card') || str_contains($method, 'credit')) return 'bi bi-credit-card-2-front-fill';
        if (str_contains($method, 'bank') || str_contains($method, 'wire')) return 'bi bi-bank';
        if (str_contains($method, 'apple')) return 'bi bi-apple';
        if (str_contains($method, 'google')) return 'bi bi-google';
        return 'bi bi-wallet2';
    }

    public function scopeCompleted($query) {
        return $query->where('status', 'completed');
    }

    public function scopeSearch($query, ?string $term) {
        if (empty($term)) {
            return $query;
        }
        return $query->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
              ->orWhere('customer_name', 'like', "%{$term}%")
              ->orWhere('customer_email', 'like', "%{$term}%")
              ->orWhere('payment_method', 'like', "%{$term}%")
              ->orWhere('status', 'like', "%{$term}%");
        });
    }
}
