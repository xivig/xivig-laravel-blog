<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TimezoneHelper;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller {
    public function edit() {
        $user = Auth::user();
        $timezones = TimezoneHelper::list();
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        return view('admin.profile.index', compact('user', 'timezones', 'recentActivities'));
    }

    public function update(Request $request) {
        $user = Auth::user();

        $data = $request->validate([
            'name'        => 'required|string|max:120',
            'email'       => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'designation' => 'nullable|string|max:150',
            'bio'         => 'nullable|string|max:1000',
            'phone'       => 'nullable|string|max:50',
            'timezone'    => 'nullable|string|max:100',
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $data['avatar'] = '/storage/' . $path;

            // Direct mirror to public_path('storage/avatars') for environments without junction/symlinks
            $publicDir = public_path('storage/avatars');
            if (!file_exists($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
            @copy($file->getRealPath(), $publicDir . '/' . $filename);
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        // Synchronize with Author persona if linked
        if ($user->author) {
            $user->author->update([
                'name'        => $user->name,
                'email'       => $user->email,
                'designation' => $user->designation ?? $user->author->designation,
                'bio'         => $user->bio ?? $user->author->bio,
                'avatar'      => $user->avatar ?? $user->author->avatar,
            ]);
        }

        ActivityLog::record('updated', 'Updated personal profile details', $user);

        return back()->with('success', 'Your profile details have been saved.');
    }

    public function uploadAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $user = Auth::user();
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');
        $avatarUrl = '/storage/' . $path;

        // Direct mirror to public_path('storage/avatars') for environments without junction/symlinks
        $publicDir = public_path('storage/avatars');
        if (!file_exists($publicDir)) {
            @mkdir($publicDir, 0755, true);
        }
        @copy($file->getRealPath(), $publicDir . '/' . $filename);

        $user->update(['avatar' => $avatarUrl]);

        if ($user->author) {
            $user->author->update(['avatar' => $avatarUrl]);
        }

        ActivityLog::record('updated', 'Uploaded new profile photograph', $user);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'avatar_url' => $user->avatar_url,
                'message'    => 'Profile photograph updated successfully.',
            ]);
        }

        return back()->with('success', 'Profile photograph updated successfully.');
    }

    public function removeAvatar() {
        $user = Auth::user();
        $user->update(['avatar' => null]);
        if ($user->author) {
            $user->author->update(['avatar' => null]);
        }
        ActivityLog::record('updated', 'Removed profile photograph', $user);

        return back()->with('success', 'Profile photograph removed.');
    }

    public function updatePassword(Request $request) {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        ActivityLog::record('security', 'Modified account authentication password', $user);

        return back()->with('success', 'Your password has been changed securely.');
    }

    public function toggleTwoFactor(Request $request) {
        $user = Auth::user();
        $user->two_factor_enabled = !$user->two_factor_enabled;
        $user->save();

        $status = $user->two_factor_enabled ? 'enabled' : 'disabled';
        ActivityLog::record('security', "Two-factor authentication {$status}", $user);

        return back()->with('success', "Two-Factor Authentication (2FA) is now {$status}.");
    }

    public function destroyOtherSessions(Request $request) {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Password incorrect for session invalidation.']);
        }

        Auth::logoutOtherDevices($request->password);
        ActivityLog::record('security', 'Terminated other active device sessions', Auth::user());

        return back()->with('success', 'All other browser sessions have been logged out.');
    }
}
