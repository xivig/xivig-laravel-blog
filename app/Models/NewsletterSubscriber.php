<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscriber extends Model {
    use SoftDeletes;

    protected $fillable = [
        'email',
        'name',
        'tier',
        'status',
        'source',
        'ip_address',
        'notes',
        'last_broadcast_at',
    ];

    protected $casts = [
        'last_broadcast_at' => 'datetime',
    ];

    public function scopeActive($query) {
        return $query->where('status', 'subscribed');
    }

    public function scopeVip($query) {
        return $query->where('tier', 'vip');
    }

    public function getDisplayNameAttribute(): string {
        return !empty($this->name) ? $this->name : 'Patron Reader';
    }

    public function getTierBadgeAttribute(): array {
        return match ($this->tier) {
            'vip'     => ['label' => 'VIP Atelier Patron', 'class' => 'bg-warning-subtle text-warning border border-warning-subtle'],
            'press'   => ['label' => 'Press & Syndicate', 'class' => 'bg-info-subtle text-info border border-info-subtle'],
            'partner' => ['label' => 'Maison Partner', 'class' => 'bg-primary-subtle text-primary border border-primary-subtle'],
            default   => ['label' => 'Gazette Reader', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'],
        };
    }

    public function getStatusBadgeAttribute(): array {
        return match ($this->status) {
            'subscribed'   => ['label' => 'Subscribed', 'class' => 'bg-success-subtle text-success border border-success-subtle'],
            'unsubscribed' => ['label' => 'Unsubscribed', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle'],
            'pending'      => ['label' => 'Pending Confirmation', 'class' => 'bg-warning-subtle text-warning border border-warning-subtle'],
            'bounced'      => ['label' => 'Bounced', 'class' => 'bg-dark-subtle text-muted border border-dark-subtle'],
            default        => ['label' => ucfirst($this->status), 'class' => 'bg-light text-muted border'],
        };
    }
}
