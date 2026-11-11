<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Mail\TwoFactorOtpMail;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller {
    public function showLogin() {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function showRegister() {
        return redirect()->route('admin.login')
            ->with('error', 'Public staff registration is disabled. Staff accounts must be provisioned by a System Administrator.');
    }

    public function register(Request $request) {
        ActivityLog::record('registration_rejected', "Rejected public registration attempt from IP: " . $request->ip(), null);

        return redirect()->route('admin.login')
            ->with('error', 'Public staff registration is disabled. Staff accounts must be provisioned by a System Administrator.');
    }

    public function login(Request $request) {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required',
        ];

        if (!config('captcha.disable')) {
            $rules['captcha'] = 'required|captcha';
        }

        $validated = $request->validate($rules);
        $email = strtolower(trim($validated['email']));
        $throttleKey = Str::transliterate($email . '|' . $request->ip());

        // Login Throttling & Lockout Protection (Max 5 attempts / 60 seconds)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            ActivityLog::record('login_lockout', "Account lockout triggered for {$email} from IP {$request->ip()}", null);

            return back()->withErrors([
                'email' => "Too many authentication attempts. Your access is locked for {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $credentials = [
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ];

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!in_array($user->role, ['admin', 'editor'])) {
                Auth::logout();
                RateLimiter::hit($throttleKey, 60);
                ActivityLog::record('login_unauthorized', "Unauthorized role access attempt by {$email}", null);
                return back()->withErrors(['email' => 'Unauthorized account access privileges.']);
            }

            // Two-Factor Authentication Interceptor
            if ($user->two_factor_enabled) {
                // Cryptographically secure OTP generation & hashed storage
                $twoFactorCode = (string) random_int(100000, 999999);
                $user->two_factor_code = Hash::make($twoFactorCode);
                $user->two_factor_expires_at = now()->addMinutes(10);
                $user->save();

                // Dispatch Email Notification
                try {
                    Mail::to($user->email)->send(new TwoFactorOtpMail($user, $twoFactorCode));
                } catch (\Throwable $e) {
                    Log::error("Failed to dispatch 2FA email notification: " . $e->getMessage());
                }

                Auth::logout();

                $request->session()->put('auth.2fa.id', $user->id);
                $request->session()->put('auth.2fa.remember', $remember);
                $request->session()->put('auth.2fa.attempts', 0);

                ActivityLog::record('2fa_challenge', "Two-factor verification challenge dispatched for {$user->email}", $user);

                return redirect()->route('admin.two-factor')
                    ->with('status', 'A 6-digit cryptographic verification code has been dispatched to your registered email address.');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            ActivityLog::record('login', "User {$user->name} authenticated into administration suite", $user);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome to Maison Gazette Administration, ' . $user->name);
        }

        RateLimiter::hit($throttleKey, 60);
        ActivityLog::record('login_failed', "Failed authentication attempt for {$email} from IP {$request->ip()}", null);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request) {
        $user = Auth::user();
        if ($user) {
            ActivityLog::record('logout', "User {$user->name} terminated session", $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been securely logged out.');
    }

    // --- TWO-FACTOR AUTHENTICATION ---

    public function showTwoFactor(Request $request) {
        $userId = $request->session()->get('auth.2fa.id');
        if (!$userId) {
            return redirect()->route('admin.login');
        }

        $user = User::findOrFail($userId);

        // Ensure an unexpired OTP exists; if expired or missing, generate a fresh cryptographic OTP
        if (empty($user->two_factor_code) || !$user->two_factor_expires_at || $user->two_factor_expires_at->isPast()) {
            $twoFactorCode = (string) random_int(100000, 999999);
            $user->two_factor_code = Hash::make($twoFactorCode);
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                Mail::to($user->email)->send(new TwoFactorOtpMail($user, $twoFactorCode));
            } catch (\Throwable $e) {
                Log::error("Failed to dispatch 2FA email notification: " . $e->getMessage());
            }
        }

        return view('admin.auth.two-factor-authentication', compact('user'));
    }

    public function verifyTwoFactor(Request $request) {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = $request->session()->get('auth.2fa.id');
        if (!$userId) {
            return redirect()->route('admin.login');
        }

        $user = User::findOrFail($userId);

        // Track and enforce 2FA verification attempts (Max 5 attempts)
        $attempts = (int) $request->session()->get('auth.2fa.attempts', 0) + 1;
        $request->session()->put('auth.2fa.attempts', $attempts);

        $submittedCode = preg_replace('/\D/', '', (string) $request->code);

        // Cryptographically verify code against securely stored hash
        if ($user->two_factor_code && Hash::check($submittedCode, $user->two_factor_code) && $user->two_factor_expires_at && $user->two_factor_expires_at->isFuture()) {
            $user->update([
                'two_factor_code'       => null,
                'two_factor_expires_at' => null,
            ]);

            $remember = $request->session()->get('auth.2fa.remember', false);
            Auth::login($user, $remember);

            $request->session()->forget(['auth.2fa.id', 'auth.2fa.remember', 'auth.2fa.attempts']);
            $request->session()->regenerate();

            ActivityLog::record('login', "User {$user->name} verified two-factor authentication", $user);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Two-Factor Authentication verified. Welcome back, ' . $user->name);
        }

        // If maximum attempts reached, lockout and invalidate security challenge
        if ($attempts >= 5) {
            $user->update([
                'two_factor_code'       => null,
                'two_factor_expires_at' => null,
            ]);
            $request->session()->forget(['auth.2fa.id', 'auth.2fa.remember', 'auth.2fa.attempts']);
            ActivityLog::record('2fa_lockout', "Two-factor verification exceeded maximum attempts for {$user->email}", $user);

            return redirect()->route('admin.login')
                ->with('error', 'Too many invalid verification attempts. The security challenge has been invalidated. Please sign in again.');
        }

        $remaining = max(0, 5 - $attempts);
        ActivityLog::record('2fa_failed_attempt', "Failed 2FA code submission for {$user->email} ({$attempts}/5)", $user);

        return back()->withErrors([
            'code' => "The provided verification code is invalid or has expired. ({$remaining} attempts remaining before session termination.)",
        ]);
    }

    public function resendTwoFactor(Request $request) {
        $userId = $request->session()->get('auth.2fa.id');
        if (!$userId) {
            return redirect()->route('admin.login');
        }

        $user = User::findOrFail($userId);
        $twoFactorCode = (string) random_int(100000, 999999);
        $user->two_factor_code = Hash::make($twoFactorCode);
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new TwoFactorOtpMail($user, $twoFactorCode));
        } catch (\Throwable $e) {
            Log::error("Failed to resend 2FA email notification: " . $e->getMessage());
        }

        ActivityLog::record('2fa_resend', "Resent two-factor verification code for {$user->email}", $user);

        return back()->with('status', 'A new 6-digit cryptographic verification code has been dispatched to your email address.');
    }

    // --- LOCKSCREEN ---

    public function lockscreen(Request $request) {
        $user = Auth::user();

        if ($user) {
            $request->session()->put('auth.lock.id', $user->id);
            ActivityLog::record('lock', "User {$user->name} locked administrative session", $user);
            Auth::logout();
        } else {
            $userId = $request->session()->get('auth.lock.id');
            if ($userId) {
                $user = User::find($userId);
            }
        }

        if (!$user) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.lockscreen', compact('user'));
    }

    public function unlock(Request $request) {
        $request->validate([
            'password' => 'required|string',
        ]);

        $userId = $request->session()->get('auth.lock.id');
        if (!$userId) {
            return redirect()->route('admin.login');
        }

        $user = User::findOrFail($userId);

        if (Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->forget('auth.lock.id');
            $request->session()->regenerate();

            ActivityLog::record('unlock', "User {$user->name} unlocked administrative session", $user);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Session unlocked. Welcome back, ' . $user->name);
        }

        return back()->withErrors(['password' => 'Invalid password credentials.']);
    }

    // --- FORGOT & RESET PASSWORD ---

    public function showForgotPassword() {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request) {
        $throttleKey = 'password-reset|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many password reset requests. Please retry in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($throttleKey, 300); // 5 minutes window

        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();
        if ($user && in_array($user->role, ['admin', 'editor'])) {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            $resetUrl = route('admin.password.reset', ['token' => $token, 'email' => $user->email]);

            try {
                Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
            } catch (\Throwable $e) {
                Log::error("Failed to dispatch password reset email to {$user->email}: " . $e->getMessage());
            }

            ActivityLog::record('password_reset_request', "Password reset link securely dispatched to {$user->email}", $user);
        }

        // Generic response to prevent user enumeration attacks
        return back()->with('status', 'If an active administrative account is associated with that email address, a secure passkey reset link has been dispatched to the inbox.');
    }

    public function showResetPassword(Request $request, $token) {
        $email = $request->query('email');
        return view('admin.auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request) {
        $throttleKey = 'password-reset-attempt|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many invalid reset attempts. Please retry in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($throttleKey, 300);

        $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($request->email));
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'This password reset passkey token is invalid or has expired.']);
        }

        // Enforce 60-minute token expiration limit
        if (isset($record->created_at) && \Carbon\Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'This password reset token has expired. Please request a new passkey link.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No active user account corresponds to this email address.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        RateLimiter::clear($throttleKey);

        ActivityLog::record('password_reset_completed', "Password was securely reset for {$user->email}", $user);

        return redirect()->route('admin.login')->with('success', 'Your password has been successfully updated. Please authenticate with your new passkey.');
    }
}
