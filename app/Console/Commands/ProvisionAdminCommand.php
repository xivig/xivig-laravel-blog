<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProvisionAdminCommand extends Command {
    protected $signature = 'admin:provision
                            {--name= : Full name of the administrator}
                            {--email= : Administrative email address}
                            {--password= : Password for the account (min 8 characters)}
                            {--role=admin : Account role: admin or editor}
                            {--random-password : Generate a cryptographically secure 24-character random password}';

    protected $description = 'Provision a new administrative or editorial account with secure credentials';

    public function handle(): int {
        $this->newLine();
        $this->line('<fg=yellow;options=bold>===============================================================</>');
        $this->line('<fg=yellow;options=bold>    MAISON GAZETTE &bull; SECURE ADMINISTRATOR PROVISIONING    </>');
        $this->line('<fg=yellow;options=bold>===============================================================</>');
        $this->newLine();

        // 1. Resolve Name
        $name = $this->option('name') ?: $this->ask('Enter administrator full name', 'Maison Administrator');

        // 2. Resolve Email
        $email = $this->option('email');
        while (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $this->ask('Enter administrative email address');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('Please enter a valid email address.');
            }
        }
        $email = strtolower(trim($email));

        // 3. Resolve Role
        $role = strtolower((string) $this->option('role'));
        if (!in_array($role, ['admin', 'editor'])) {
            $role = $this->choice('Select account role', ['admin', 'editor'], 0);
        }

        // 4. Resolve Password
        $password = $this->option('password');
        $isGenerated = false;

        if ($this->option('random-password')) {
            $password = Str::password(24, true, true, true, false);
            $isGenerated = true;
        } elseif (empty($password)) {
            $generateChoice = $this->confirm('Would you like to generate a cryptographically strong 24-character random password?', true);
            if ($generateChoice) {
                $password = Str::password(24, true, true, true, false);
                $isGenerated = true;
            } else {
                while (empty($password) || strlen($password) < 8) {
                    $password = $this->secret('Enter account password (min 8 characters)');
                    if (strlen($password) < 8) {
                        $this->error('Password must be at least 8 characters long.');
                        continue;
                    }
                    $confirm = $this->secret('Confirm password');
                    if ($password !== $confirm) {
                        $this->error('Passwords do not match. Please try again.');
                        $password = null;
                    }
                }
            }
        } elseif (strlen($password) < 8) {
            $this->error('The provided --password must be at least 8 characters.');
            return self::FAILURE;
        }

        // 5. Check if user already exists
        $user = User::where('email', $email)->first();
        if ($user) {
            $confirmUpdate = $this->confirm("An account with email [{$email}] already exists. Overwrite credentials?", false);
            if (!$confirmUpdate) {
                $this->warn('Operation aborted by operator.');
                return self::SUCCESS;
            }

            $user->update([
                'name'     => $name,
                'password' => Hash::make($password),
                'role'     => $role,
            ]);

            $this->info("Existing account [{$email}] successfully updated with new credentials.");
        } else {
            $user = User::create([
                'name'        => $name,
                'email'       => $email,
                'password'    => Hash::make($password),
                'role'        => $role,
                'designation' => $role === 'admin' ? 'Executive Administrator' : 'Staff Editor',
                'timezone'    => 'Europe/Paris',
            ]);

            $this->info("New administrative account [{$email}] successfully provisioned.");
        }

        // Assign Spatie/RBAC role if table exists
        try {
            $roleName = $role === 'admin' ? 'super_admin' : 'editor';
            $rbacRole = Role::where('name', $roleName)->first();
            if ($rbacRole && method_exists($user, 'roles')) {
                $user->roles()->syncWithoutDetaching([$rbacRole->id]);
            }
        } catch (\Throwable $e) {}

        // Audit Trail
        ActivityLog::record('admin_provisioned', "Administrative account provisioned via CLI for {$email} (Role: {$role})", $user);

        // Display Summary
        $this->newLine();
        $this->table(['Field', 'Value'], [
            ['Name', $user->name],
            ['Email', $user->email],
            ['Role', strtoupper($user->role)],
            ['Password', $isGenerated ? $password : '[Supplied by operator]'],
            ['Status', 'Active / Provisioned'],
        ]);

        if ($isGenerated) {
            $this->warn('===============================================================');
            $this->warn('IMPORTANT: Copy and store this generated password in a safe vault.');
            $this->warn("Password: {$password}");
            $this->warn('===============================================================');
        }

        $this->newLine();
        return self::SUCCESS;
    }
}
