<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceInquiry extends Model {
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'name',
        'email',
        'company',
        'phone',
        'budget',
        'timeline',
        'message',
        'status',
        'notes',
    ];

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function scopePending($query) {
        return $query->where('status', 'pending');
    }

    public function getStatusBadgeAttribute(): array {
        return match ($this->status) {
            'pending'   => ['label' => 'New / Pending', 'class' => 'bg-warning-subtle text-warning border border-warning-subtle'],
            'reviewed'  => ['label' => 'Under Review', 'class' => 'bg-info-subtle text-info border border-info-subtle'],
            'contacted' => ['label' => 'Client Contacted', 'class' => 'bg-primary-subtle text-primary border border-primary-subtle'],
            'converted' => ['label' => 'Commission Retained', 'class' => 'bg-success-subtle text-success border border-success-subtle'],
            'archived'  => ['label' => 'Archived', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'],
            default     => ['label' => ucfirst($this->status), 'class' => 'bg-light text-muted border'],
        };
    }
}
