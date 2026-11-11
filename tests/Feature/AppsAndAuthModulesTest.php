<?php

namespace Tests\Feature;

use App\Mail\TwoFactorOtpMail;
use App\Models\CalendarEvent;
use App\Models\FileManagerFile;
use App\Models\FileManagerFolder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppsAndAuthModulesTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;
    protected string $adminPassword = 'admin123456';

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    public function test_calendar_page_and_crud_lifecycle(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.calendar.index'));
        $response->assertStatus(200);
        $response->assertSee('Editorial Calendar & Schedules');

        // Create Event
        $createResponse = $this->actingAs($this->admin)->postJson(route('admin.calendar.store'), [
            'title'   => 'Haute Couture Gala',
            'start'   => now()->addDays(2)->format('Y-m-d H:i:s'),
            'color'   => 'primary',
            'allDay'  => true,
        ]);
        $createResponse->assertStatus(201);
        $this->assertDatabaseHas('calendar_events', ['title' => 'Haute Couture Gala']);
        $event = CalendarEvent::where('title', 'Haute Couture Gala')->firstOrFail();

        // Get Events JSON
        $eventsResponse = $this->actingAs($this->admin)->getJson(route('admin.calendar.events'));
        $eventsResponse->assertStatus(200);
        $eventsResponse->assertJsonFragment(['title' => 'Haute Couture Gala']);

        // Update Event
        $updateResponse = $this->actingAs($this->admin)->putJson(route('admin.calendar.update', $event), [
            'title' => 'Haute Couture Gala - Private Showing',
            'start' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);
        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('calendar_events', ['title' => 'Haute Couture Gala - Private Showing']);

        // Delete Event
        $deleteResponse = $this->actingAs($this->admin)->deleteJson(route('admin.calendar.destroy', $event));
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('calendar_events', ['id' => $event->id]);
    }

    public function test_task_manager_page_and_status_transitions(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.task-manager.index'));
        $response->assertStatus(200);
        $response->assertSee('Architect.OS');

        // Store Task
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.task-manager.store'), [
            'title'        => 'Review Editorial Layout Draft',
            'avatar_badge' => 'DS',
            'badge_color'  => 'gold',
            'status'       => 'sprint',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'Review Editorial Layout Draft']);
        $task = Task::where('title', 'Review Editorial Layout Draft')->firstOrFail();

        // Update Status to Queue
        $updateResponse = $this->actingAs($this->admin)->putJson(route('admin.task-manager.update-status', $task), [
            'status' => 'queue',
        ]);
        $updateResponse->assertStatus(200);
        $this->assertEquals('queue', $task->fresh()->status);

        // Delete Task
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.task-manager.destroy', $task));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_file_manager_page_folders_and_files(): void {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->get(route('admin.file-manager.index'));
        $response->assertStatus(200);
        $response->assertSee('Maison Cloud');

        // Create Folder
        $createFolderResponse = $this->actingAs($this->admin)->post(route('admin.file-manager.folder'), [
            'name' => 'Spring 2027 Campaign',
        ]);
        $createFolderResponse->assertRedirect();
        $this->assertDatabaseHas('file_manager_folders', ['name' => 'Spring 2027 Campaign']);
        $folder = FileManagerFolder::where('name', 'Spring 2027 Campaign')->firstOrFail();

        // Rename Folder
        $renameResponse = $this->actingAs($this->admin)->put(route('admin.file-manager.folder.rename', $folder->id), [
            'name' => 'Spring-Summer 2027 Lookbook',
        ]);
        $renameResponse->assertRedirect();
        $this->assertDatabaseHas('file_manager_folders', ['name' => 'Spring-Summer 2027 Lookbook']);

        // Upload File
        $fakeFile = UploadedFile::fake()->create('press_release.pdf', 250, 'application/pdf');
        $uploadResponse = $this->actingAs($this->admin)->post(route('admin.file-manager.upload'), [
            'file'      => $fakeFile,
            'folder_id' => $folder->id,
        ]);
        $uploadResponse->assertRedirect();
        $this->assertDatabaseHas('file_manager_files', ['name' => 'press_release.pdf']);
        $file = FileManagerFile::where('name', 'press_release.pdf')->firstOrFail();

        // Soft Delete File & Restore
        $deleteFileResponse = $this->actingAs($this->admin)->delete(route('admin.file-manager.file.delete', $file->id));
        $deleteFileResponse->assertRedirect();
        $this->assertSoftDeleted('file_manager_files', ['id' => $file->id]);

        $restoreFileResponse = $this->actingAs($this->admin)->post(route('admin.file-manager.file.restore', $file->id));
        $restoreFileResponse->assertRedirect();
        $this->assertFalse($file->fresh()->trashed());

        // Soft Delete Folder
        $deleteFolderResponse = $this->actingAs($this->admin)->delete(route('admin.file-manager.folder.delete', $folder->id));
        $deleteFolderResponse->assertRedirect();
        $this->assertSoftDeleted('file_manager_folders', ['id' => $folder->id]);

        // Force Delete Folder
        $forceDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.file-manager.folder.forceDelete', $folder->id));
        $forceDeleteResponse->assertRedirect();
        $this->assertDatabaseMissing('file_manager_folders', ['id' => $folder->id]);
    }

    public function test_image_cropper_page_renders_successfully(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.image-cropper.index'));
        $response->assertStatus(200);
        $response->assertSee('Advanced Image Cropper');
        $response->assertSee('Live Preview');
        $response->assertSee('Spatial Telemetry');
    }

    public function test_lockscreen_and_unlock_flow(): void {
        // Authenticated user locks session
        $response = $this->actingAs($this->admin)->get(route('admin.lockscreen'));
        $response->assertStatus(200);
        $response->assertSee('Session Suspended');
        $response->assertSee($this->admin->name);
        $this->assertGuest();

        // Wrong password fails
        $failResponse = $this->post(route('admin.unlock'), ['password' => 'wrong-pass']);
        $failResponse->assertSessionHasErrors('password');
        $this->assertGuest();

        // Correct password restores session
        $unlockResponse = $this->post(route('admin.unlock'), ['password' => $this->adminPassword]);
        $unlockResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_forgot_password_and_reset_flow(): void {
        // View forgot password form
        $viewResponse = $this->get(route('admin.password.request'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Passkey Recovery');

        // Request reset link
        $sendResponse = $this->post(route('admin.password.email'), [
            'email' => $this->admin->email,
        ]);
        $sendResponse->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $this->admin->email]);

        // Mock token for reset
        $token = 'test-token-123456';
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->admin->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // View reset password form
        $resetView = $this->get(route('admin.password.reset', ['token' => $token, 'email' => $this->admin->email]));
        $resetView->assertStatus(200);
        $resetView->assertSee('Update Passkey');

        // Submit new password
        $newPass = 'NewSecret123!';
        $updateResponse = $this->post(route('admin.password.update'), [
            'token'                 => $token,
            'email'                 => $this->admin->email,
            'password'              => $newPass,
            'password_confirmation' => $newPass,
        ]);
        $updateResponse->assertRedirect(route('admin.login'));
        $updateResponse->assertSessionHas('success');

        $this->assertTrue(Hash::check($newPass, $this->admin->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $this->admin->email]);
    }

    public function test_two_factor_authentication_flow(): void {
        Mail::fake();

        // Enable 2FA on admin
        $this->admin->update(['two_factor_enabled' => true]);

        // Attempt login -> intercepted by 2FA
        $loginResponse = $this->post(route('admin.login.submit'), [
            'email'    => $this->admin->email,
            'password' => $this->adminPassword,
        ]);
        $loginResponse->assertRedirect(route('admin.two-factor'));
        $this->assertGuest();

        // Verify email was dispatched with cryptographic OTP
        Mail::assertSent(TwoFactorOtpMail::class, function ($mail) {
            return $mail->hasTo($this->admin->email) && strlen($mail->otp) === 6;
        });

        // View 2FA challenge screen
        $challengeResponse = $this->get(route('admin.two-factor'));
        $challengeResponse->assertStatus(200);
        $challengeResponse->assertSee('Two-Step Verification');
        $challengeResponse->assertDontSee('DEV MODE OTP');

        $this->admin->refresh();
        $hashedCode = $this->admin->two_factor_code;
        $this->assertNotEmpty($hashedCode);
        $this->assertStringStartsWith('$2y$', $hashedCode); // Verified bcrypt hashed storage

        // Submit invalid code -> error
        $failCodeResponse = $this->post(route('admin.two-factor.verify'), ['code' => '000000']);
        $failCodeResponse->assertSessionHasErrors('code');
        $this->assertGuest();

        // Resend code
        $resendResponse = $this->post(route('admin.two-factor.resend'));
        $resendResponse->assertSessionHas('status');
        $statusMsg = session('status');
        $this->assertStringNotContainsString('Dev Code:', $statusMsg); // No OTP leak in flash

        // Capture newly resent OTP from mail
        $capturedOtp = null;
        Mail::assertSent(TwoFactorOtpMail::class, function ($mail) use (&$capturedOtp) {
            $capturedOtp = $mail->otp;
            return true;
        });

        $this->assertNotNull($capturedOtp);
        $this->admin->refresh();
        $this->assertTrue(Hash::check($capturedOtp, $this->admin->two_factor_code));

        // Submit valid plain code -> authenticate & redirect to dashboard
        $verifyResponse = $this->post(route('admin.two-factor.verify'), ['code' => $capturedOtp]);
        $verifyResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_login_page_renders_with_math_captcha(): void {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Security Verification');
        $response->assertSee('Solve math challenge');
        $response->assertSee('captcha/math');
    }

    public function test_register_page_and_user_creation(): void {
        // Public registration is disabled by default
        $response = $this->get(route('admin.register'));
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('error');

        // Direct POST to registration endpoint is rejected
        $registerResponse = $this->post(route('admin.register.submit'), [
            'name'                  => 'Eleanor Sterling',
            'email'                 => 'eleanor@xivig.com',
            'password'              => 'Elegance2027!',
            'password_confirmation' => 'Elegance2027!',
        ]);

        $registerResponse->assertRedirect(route('admin.login'));
        $this->assertDatabaseMissing('users', ['email' => 'eleanor@xivig.com']);
        $this->assertGuest();

        // Staff accounts must be created by an authenticated administrator
        $adminCreateResponse = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'                  => 'Eleanor Sterling',
            'email'                 => 'eleanor@xivig.com',
            'password'              => 'Elegance2027!',
            'password_confirmation' => 'Elegance2027!',
            'role'                  => 'editor',
        ]);
        $adminCreateResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'eleanor@xivig.com', 'role' => 'editor']);
    }

    public function test_captcha_enforcement_in_login_and_register(): void {
        config(['captcha.disable' => false]);

        // Login fails without captcha when enabled
        $loginFail = $this->post(route('admin.login.submit'), [
            'email'    => $this->admin->email,
            'password' => $this->adminPassword,
        ]);
        $loginFail->assertSessionHasErrors('captcha');
    }
}
