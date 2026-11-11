<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model {
    protected $fillable = ['name', 'guard_name', 'display_name', 'description'];

    public function permissions(): BelongsToMany {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    public function users(): BelongsToMany {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }

    public function givePermissionTo(string|Permission $permission): void {
        $perm = is_string($permission) ? Permission::firstOrCreate(['name' => $permission]) : $permission;
        $this->permissions()->syncWithoutDetaching([$perm->id]);
    }

    public function hasPermissionTo(string $permission): bool {
        return $this->permissions()->where('name', $permission)->exists();
    }
}
