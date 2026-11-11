<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetAdminPasswordCommand extends Command {
    protected $signature = 'admin:reset-password
                            {email? : The administrative user email}
                            {--password= : The new password (optional)}
                            {--random : Generate a cryptographically secure random password}';

    protected $description = 'Reset the password for an administrative user via CLI';

    public function handle(): int {
        $email = $this->argument('email');
        if (empty($email)) {
            $email = $this->ask('Enter administrative account email');
        }

        $user = User::where('email', strtolower(trim($email)))->first();
        if (!$user) {
            $this->error("No user found with email [{$email}].");
            return self::FAILURE;
        }

        $password = $this->option('password');
        $isGenerated = false;

        if ($this->option('random')) {
            $password = Str::password(24, true, true, true, false);
            $isGenerated = true;
        } elseif (empty($password)) {
            $choice = $this->confirm('Generate a random secure 24-character password?', true);
            if ($choice) {
                $password = Str::password(24, true, true, true, false);
                $isGenerated = true;
            } else {
                while (empty($password) || strlen($password) < 8) {
                    $password = $this->secret('Enter new password (min 8 characters)');
                    $confirm = $this->secret('Confirm new password');
                    if ($password !== $confirm) {
                        $this->error('Passwords do not match.');
                        $password = null;
                    }
                }
            }
        }

        $user->update([
            'password'              => Hash::make($password),
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
        ]);

        ActivityLog::record('admin_password_reset_cli', "Password reset via CLI for {$user->email}", $user);

        $this->info("Password for [{$user->email}] successfully reset.");
        if ($isGenerated) {
            $this->warn("New Password: {$password}");
        }

        return self::SUCCESS;
    }
}
