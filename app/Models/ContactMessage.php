<?php

namespace App\Models;

use App\Helpers\TimezoneHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'ip_address',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function scopeFilter($query, array $filters) {
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'unread') {
                $query->where('is_read', false);
            } elseif ($filters['status'] === 'read') {
                $query->where('is_read', true);
            }
        }

        return $query;
    }

    public function formattedTime($user = null): string {
        return TimezoneHelper::format($this->created_at, 'M d, Y h:i A', $user);
    }
}
