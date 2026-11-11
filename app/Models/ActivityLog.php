<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model {
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an audit activity log entry.
     */
    public static function record(string $action, string $description, ?Model $model = null, ?array $properties = null, ?int $userId = null): self {
        return static::create([
            'user_id'     => $userId ?? auth()->id(),
            'action'      => strtolower($action),
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model?->getKey(),
            'description' => $description,
            'properties'  => $properties,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    /**
     * Alias for record().
     */
    public static function log(string $action, string $description, ?Model $model = null, ?array $properties = null, ?int $userId = null): self {
        return static::record($action, $description, $model, $properties, $userId);
    }

    /**
     * Scope for filtering activity logs.
     */
    public function scopeFilter($query, array $filters) {
        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('action', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Get CSS badge styling for action type.
     */
    public function getActionBadgeClassAttribute(): string {
        return match ($this->action) {
            'login'    => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
            'logout'   => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
            'created'  => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            'updated'  => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
            'deleted'  => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
            'security' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
            'sent'     => 'bg-indigo bg-opacity-10 text-indigo border border-indigo border-opacity-25',
            default    => 'bg-dark bg-opacity-10 text-light border border-secondary border-opacity-25',
        };
    }
}
