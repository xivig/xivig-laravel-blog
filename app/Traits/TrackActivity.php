<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait TrackActivity {
    public static function bootTrackActivity(): void {
        static::created(function ($model) {
            $model->logModelActivity('created');
        });

        static::updated(function ($model) {
            $model->logModelActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logModelActivity('deleted');
        });
    }

    public function logModelActivity(string $action): void {
        try {
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => $action,
                'model_type'  => get_class($this),
                'model_id'    => $this->getKey(),
                'description' => "Record {$action} on " . class_basename($this) . " (#{$this->getKey()})",
                'ip_address'  => request()->ip() ?? '127.0.0.1',
                'user_agent'  => request()->userAgent() ?? 'CLI/System',
                'properties'  => $this->getDirty(),
            ]);
        } catch (\Throwable $e) {
            // Silently allow model action if logs table not ready
        }
    }
}
