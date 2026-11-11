<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller {
    public function index() {
        // Ensure standard roles exist
        $editorRole = Role::firstOrCreate(['name' => 'editor'], ['display_name' => 'Editor', 'description' => 'Editorial manager with publishing & curation authority.']);
        $authorRole = Role::firstOrCreate(['name' => 'author'], ['display_name' => 'Author', 'description' => 'Staff author creating and editing draft chronicles.']);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['display_name' => 'Patron User', 'description' => 'Standard verified subscriber with discourse access.']);

        $roles = Role::with(['permissions', 'users'])->get();
        $users = User::with('roles')->paginate(20);

        // Core Matrix Permissions (Matching Image 2)
        $matrixAuthorities = [
            [
                'key'         => 'manage-users',
                'name'        => 'manage_users',
                'title'       => 'Manage Users',
                'description' => 'Create, edit and delete users.',
            ],
            [
                'key'         => 'manage-authors',
                'name'        => 'manage_authors',
                'title'       => 'Manage Authors',
                'description' => 'Manage author profiles.',
            ],
            [
                'key'         => 'create-posts',
                'name'        => 'create_posts',
                'title'       => 'Create Posts',
                'description' => 'Create new blog posts.',
            ],
            [
                'key'         => 'edit-posts',
                'name'        => 'edit_posts',
                'title'       => 'Edit Posts',
                'description' => 'Edit existing blog posts.',
            ],
            [
                'key'         => 'delete-posts',
                'name'        => 'delete_posts',
                'title'       => 'Delete Posts',
                'description' => 'Delete blog posts.',
            ],
            [
                'key'         => 'publish-posts',
                'name'        => 'publish_posts',
                'title'       => 'Publish Posts',
                'description' => 'Change post status to published.',
            ],
            [
                'key'         => 'manage-categories',
                'name'        => 'manage_categories',
                'title'       => 'Manage Categories',
                'description' => 'Organize collection taxonomy structure.',
            ],
            [
                'key'         => 'manage-comments',
                'name'        => 'manage_comments',
                'title'       => 'Manage Comments',
                'description' => 'Moderate reader discourse and reviews.',
            ],
            [
                'key'         => 'manage-ads',
                'name'        => 'manage_ads',
                'title'       => 'Manage Monetization & Ads',
                'description' => 'Configure sponsored campaigns and ad slots.',
            ],
        ];

        // Seed default authorities if not yet in database
        foreach ($matrixAuthorities as $auth) {
            Permission::firstOrCreate(['name' => $auth['name']]);
        }

        // Build active permission map per role: [role_name => [perm_name => true/false]]
        $rolePermissionMap = [
            'editor' => $editorRole->permissions->pluck('name')->toArray(),
            'author' => $authorRole->permissions->pluck('name')->toArray(),
            'user'   => $userRole->permissions->pluck('name')->toArray(),
        ];

        return view('admin.roles.index', compact(
            'roles',
            'users',
            'matrixAuthorities',
            'rolePermissionMap',
            'editorRole',
            'authorRole',
            'userRole'
        ));
    }

    public function assign(Request $request, User $user) {
        $request->validate(['role' => 'required|exists:roles,name']);
        $user->assignRole($request->input('role'));
        $user->update(['role' => $request->input('role')]);

        ActivityLog::record('updated', "Reassigned role of {$user->name} to {$request->input('role')}", $user);

        return back()->with('success', "Role {$request->input('role')} assigned to {$user->name}.");
    }

    public function togglePermission(Request $request) {
        $request->validate([
            'role'       => 'required|string',
            'permission' => 'required|string',
            'enabled'    => 'required|boolean',
        ]);

        $role = Role::where('name', $request->role)->firstOrFail();
        $permName = str_replace('-', '_', $request->permission);

        $perm = Permission::firstOrCreate(['name' => $permName]);

        if ($request->boolean('enabled')) {
            $role->permissions()->syncWithoutDetaching([$perm->id]);
        } else {
            $role->permissions()->detach($perm->id);
        }

        ActivityLog::record('updated', "Updated permission '{$permName}' for role '{$role->name}'", $role);

        return response()->json([
            'success' => true,
            'message' => "Permission updated for " . ucfirst($role->name),
        ]);
    }
}
