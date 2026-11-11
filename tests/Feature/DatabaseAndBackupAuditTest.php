<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Backup;
use App\Models\User;
use App\Services\BackupService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseAndBackupAuditTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;
    protected User $editor;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->firstOrFail();
        $this->editor = User::create([
            'name'     => 'Staff Editor',
            'email'    => 'editor.staff@xivig.com',
            'password' => Hash::make('SecretPass123!'),
            'role'     => 'editor',
        ]);
    }

    /**
     * Test admin:provision command with explicit credentials.
     */
    public function test_admin_provision_command_creates_administrator(): void {
        $this->artisan('admin:provision', [
            '--name'     => 'New Chief Editor',
            '--email'    => 'chief.editor@xivig.com',
            '--password' => 'StrongPass2027!#',
            '--role'     => 'admin',
        ])->assertSuccessful();

        $user = User::where('email', 'chief.editor@xivig.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('New Chief Editor', $user->name);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue(Hash::check('StrongPass2027!#', $user->password));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'admin_provisioned',
        ]);
    }

    /**
     * Test admin:provision command with --random-password flag.
     */
    public function test_admin_provision_command_with_random_password(): void {
        $this->artisan('admin:provision', [
            '--name'              => 'Random Admin',
            '--email'             => 'random.admin@xivig.com',
            '--role'              => 'admin',
            '--random-password'   => true,
        ])->assertSuccessful();

        $user = User::where('email', 'random.admin@xivig.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->role);
        // Ensure password is not default
        $this->assertFalse(Hash::check('admin123456', $user->password));
    }

    /**
     * Test admin:reset-password command.
     */
    public function test_admin_reset_password_command(): void {
        $this->artisan('admin:reset-password', [
            'email'      => $this->admin->email,
            '--password' => 'BrandNewPassword2027!#',
        ])->assertSuccessful();

        $this->admin->refresh();
        $this->assertTrue(Hash::check('BrandNewPassword2027!#', $this->admin->password));
        $this->assertNull($this->admin->two_factor_code);
    }

    /**
     * Test Admin role restrictions:
     * Editors must be DENIED (403) from Backups, Users, Settings, and Roles. Only Admins can access.
     */
    public function test_backup_vault_access_restricted_to_admin_role(): void {
        // Editor access must be rejected with 403 Forbidden
        $response = $this->actingAs($this->editor)->get(route('admin.backups.index'));
        $response->assertStatus(403);

        $createResponse = $this->actingAs($this->editor)->post(route('admin.backups.create'));
        $createResponse->assertStatus(403);

        // Editor denied from Users, Settings, Roles
        $this->actingAs($this->editor)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($this->editor)->get(route('admin.settings.index'))->assertStatus(403);
        $this->actingAs($this->editor)->get(route('admin.roles.index'))->assertStatus(403);

        // Admin access must succeed (200)
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.backups.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('System Backups');

        $this->actingAs($this->admin)->get(route('admin.users.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('admin.settings.index'))->assertStatus(200);
    }

    /**
     * Test BackupController download and audit logging.
     */
    public function test_backup_controller_download_and_lifecycle(): void {
        $backup = BackupService::createDatabaseBackup();
        $safePath = storage_path('app/backups/' . $backup->filename);

        // Editor cannot download
        $editorDownload = $this->actingAs($this->editor)->get(route('admin.backups.download', $backup));
        $editorDownload->assertStatus(403);

        // Admin can download
        $adminDownload = $this->actingAs($this->admin)->get(route('admin.backups.download', $backup));
        $adminDownload->assertStatus(200);

        // Clean up test file
        if (File::exists($safePath)) {
            File::delete($safePath);
        }
    }

    /**
     * Test deploy:clean command execution and audit check.
     */
    public function test_deploy_clean_command_audit_and_cleanup(): void {
        $this->artisan('deploy:clean', ['--check' => true])
            ->assertSuccessful();

        $this->artisan('deploy:clean', ['--all' => true])
            ->assertSuccessful();
    }

    /**
     * Test .gitignore and security .htaccess configurations exist.
     */
    public function test_security_and_ignore_configurations_exist(): void {
        $this->assertTrue(File::exists(base_path('.gitignore')));
        $this->assertTrue(File::exists(base_path('.gitattributes')));
        $this->assertTrue(File::exists(database_path('.htaccess')));
        $this->assertTrue(File::exists(storage_path('.htaccess')));

        $gitignoreContent = File::get(base_path('.gitignore'));
        $this->assertStringContainsString('*.sqlite', $gitignoreContent);
        $this->assertStringContainsString('storage/app/backups', $gitignoreContent);
        $this->assertStringContainsString('storage/logs', $gitignoreContent);
        $this->assertStringContainsString('storage/framework/sessions', $gitignoreContent);
    }
}
