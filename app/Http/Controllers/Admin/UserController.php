<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller {

    public function index(Request $request) {
        $status = $request->get('status');
        $query = User::query();

        if ($status === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('designation', 'like', "%{$term}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $trashedCount = User::onlyTrashed()->count();
        $totalUsers = User::count();
        $activeCount = User::count();
        $adminsCount = User::whereIn('role', ['admin', 'super_admin'])->count();
        $editorsCount = User::where('role', 'editor')->count();
        $authorsCount = User::where('role', 'author')->count();

        return view('admin_crud_all.users.user-list', compact(
            'users',
            'trashedCount',
            'totalUsers',
            'activeCount',
            'adminsCount',
            'editorsCount',
            'authorsCount'
        ));
    }

    public function create() {
        return view('admin_crud_all.users.user-add');
    }

    public function store(Request $request) {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email',
            'password'    => 'required|string|min:8',
            'role'        => 'required|in:admin,editor,author,user',
            'designation' => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'bio'         => 'nullable|string',
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'designation' => $request->designation,
            'phone'       => $request->phone,
            'bio'         => $request->bio,
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = 'storage/' . $path;
        }

        $user = User::create($data);
        $user->assignRole($request->role);

        ActivityLog::record('user_create', "Created user account {$user->name} ({$user->email}) with role [{$user->role}]", Auth::user());

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} created successfully.");
    }

    public function show(User $user) {
        $postsCount = $user->author ? $user->author->posts()->count() : 0;
        $commentsCount = \App\Models\Comment::where('author_email', $user->email)->count();
        $recentLogs = $user->activityLogs()->latest()->take(10)->get();

        return view('admin_crud_all.users.user-details', compact('user', 'recentLogs', 'postsCount', 'commentsCount'));
    }

    public function edit(User $user) {
        return view('admin_crud_all.users.user-edit', compact('user'));
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'        => 'required|in:admin,editor,author,user',
            'password'    => 'nullable|string|min:8',
            'designation' => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'bio'         => 'nullable|string',
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->designation = $request->designation;
        $user->phone = $request->phone;
        $user->bio = $request->bio;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, 'storage/avatars/')) {
                Storage::disk('public')->delete(substr($user->avatar, 8));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = 'storage/' . $path;
        }

        $user->save();
        $user->assignRole($request->role);

        ActivityLog::record('user_update', "Updated user account {$user->name} ({$user->email})", Auth::user());

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(User $user) {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        $user->delete();
        ActivityLog::record('user_delete', "Moved user {$user->name} ({$user->email}) to trash", Auth::user());

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} moved to trash.");
    }

    public function restore($id) {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        ActivityLog::record('user_restore', "Restored user {$user->name} ({$user->email}) from trash", Auth::user());

        return back()->with('success', "User {$user->name} restored successfully.");
    }

    public function forceDelete($id) {
        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot permanently delete your own account.');
        }

        if ($user->avatar && str_starts_with($user->avatar, 'storage/avatars/')) {
            Storage::disk('public')->delete(substr($user->avatar, 8));
        }

        $name = $user->name;
        $user->forceDelete();

        ActivityLog::record('user_force_delete', "Permanently deleted user {$name}", Auth::user());

        return back()->with('success', "User {$name} permanently deleted.");
    }

    public function duplicate($id) {
        $user = User::findOrFail($id);
        $newUser = $user->replicate();
        $newUser->email = 'copy_' . uniqid() . '_' . $user->email;

        return view('admin_crud_all.users.user-add', [
            'duplicate' => $newUser,
            'original'  => $user,
            'mandatory' => ['email', 'password'],
        ]);
    }

    public function bulkAction(Request $request) {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'integer',
            'action' => 'required|in:delete,restore,force_delete',
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if (in_array($action, ['delete', 'force_delete'])) {
            $ids = array_values(array_diff($ids, [Auth::id()]));
        }

        if (empty($ids)) {
            return back()->with('error', 'No eligible users selected for this action.');
        }

        if ($action === 'delete') {
            User::whereIn('id', $ids)->delete();
            ActivityLog::record('user_bulk_delete', 'Bulk moved ' . count($ids) . ' users to trash', Auth::user());
            return back()->with('success', count($ids) . ' user accounts moved to trash.');
        }

        if ($action === 'restore') {
            User::onlyTrashed()->whereIn('id', $ids)->restore();
            ActivityLog::record('user_bulk_restore', 'Bulk restored ' . count($ids) . ' users from trash', Auth::user());
            return back()->with('success', count($ids) . ' user accounts restored successfully.');
        }

        if ($action === 'force_delete') {
            User::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            ActivityLog::record('user_bulk_force_delete', 'Bulk permanently deleted ' . count($ids) . ' users', Auth::user());
            return back()->with('success', count($ids) . ' user accounts permanently deleted.');
        }

        return back();
    }

    public function rolePermissions() {
        $users = User::orderBy('name')->get();
        $permissions = Permission::all();
        $roles = Role::with('permissions')->get();

        $rolePermissionMap = [];
        foreach ($roles as $role) {
            $rolePermissionMap[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return view('admin_crud_all.users.role-permissions', compact('users', 'permissions', 'roles', 'rolePermissionMap'));
    }

    public function updatePermission(Request $request) {
        $request->validate([
            'role'          => 'required|in:admin,editor,author,user',
            'permission_id' => 'required|exists:permissions,id',
            'action'        => 'required|in:assign,revoke',
        ]);

        $role = Role::firstOrCreate(['name' => $request->role]);
        $permission = Permission::findOrFail($request->permission_id);

        if ($request->action === 'assign') {
            $role->givePermissionTo($permission);
        } else {
            $role->revokePermissionTo($permission);
        }

        return response()->json(['success' => true]);
    }

    public function updateRole(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|in:admin,editor,author,user',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update(['role' => $request->role]);
        $user->assignRole($request->role);

        ActivityLog::record('user_role_update', "Updated role of user {$user->name} to {$request->role}", Auth::user());

        return response()->json([
            'success' => true,
            'message' => "User {$user->name} role updated to " . ucfirst($request->role) . '.',
        ]);
    }
}
