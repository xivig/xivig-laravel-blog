<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'timezone',
        'designation',
        'phone',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    public function getTimezoneAttribute($value): string {
        if (!empty($value)) {
            return $value;
        }
        return Setting::get('timezone', config('app.timezone', 'UTC'));
    }

    public function formatDate($date, string $format = 'M d, Y H:i'): ?string {
        if (!$date) {
            return null;
        }
        $c = $date instanceof \Carbon\CarbonInterface ? $date->copy() : \Carbon\Carbon::parse($date);
        return $c->setTimezone($this->timezone)->format($format);
    }

    public function getAvatarUrlAttribute(): ?string {
        if (empty($this->avatar)) {
            return null;
        }
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }
        $clean = ltrim($this->avatar, '/');
        $relativePath = str_starts_with($clean, 'storage/') ? substr($clean, 8) : $clean;

        // Auto-mirror to public/storage if missing (handles Windows non-symlink environments)
        $publicFile = public_path('storage/' . $relativePath);
        $storageFile = storage_path('app/public/' . $relativePath);

        if (!file_exists($publicFile) && file_exists($storageFile)) {
            $dir = dirname($publicFile);
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            @copy($storageFile, $publicFile);
        }

        if (!file_exists($publicFile) && !file_exists($storageFile)) {
            return null;
        }

        return '/storage/' . $relativePath;
    }

    public function getProfilePictureAttribute(): ?string {
        return $this->avatar_url;
    }

    public function posts() {
        return $this->hasManyThrough(Post::class, Author::class, 'user_id', 'author_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'author_email', 'email');
    }

    public function author() {
        return $this->hasOne(Author::class);
    }

    public function roles() {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    public function permissions() {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions', 'model_id', 'permission_id');
    }

    public function activityLogs() {
        return $this->hasMany(ActivityLog::class);
    }

    public function assignRole(string|Role $role): void {
        $r = is_string($role) ? Role::firstOrCreate(['name' => $role]) : $role;
        $this->roles()->syncWithoutDetaching([$r->id]);
    }

    public function hasRole(string $role): bool {
        if ($this->role === 'super_admin' || $this->role === 'admin') {
            return true;
        }
        if ($this->role === $role) {
            return true;
        }
        return $this->roles->contains('name', $role);
    }

    public function hasPermission(string $permission): bool {
        if ($this->hasRole('super_admin') || $this->role === 'admin') {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role->hasPermissionTo($permission)) {
                return true;
            }
        }
        return $this->permissions->contains('name', $permission);
    }

    public function isAdmin(): bool {
        return in_array($this->role, ['admin', 'super_admin']) || $this->hasRole('admin');
    }

    public function isSuperAdmin(): bool {
        return $this->role === 'super_admin' || $this->hasRole('super_admin');
    }

    public function isEditor(): bool {
        return $this->role === 'editor' || $this->hasRole('editor') || $this->isAdmin();
    }

    public function isAuthor(): bool {
        return $this->role === 'author' || $this->hasRole('author') || $this->isEditor();
    }
}
