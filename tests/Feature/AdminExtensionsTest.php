<?php

namespace Tests\Feature;

use App\Helpers\TimezoneHelper;
use App\Models\ActivityLog;
use App\Models\AdminEmail;
use App\Models\ChatMessage;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminExtensionsTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    public function test_admin_can_view_activity_logs(): void {
        ActivityLog::record('created', 'Test chronicle audit entry', null, null, $this->admin->id);

        $response = $this->actingAs($this->admin)->get(route('admin.activity-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Audit & Activity Logs');
        $response->assertSee('Test chronicle audit entry');
    }

    public function test_admin_can_filter_and_export_activity_logs(): void {
        ActivityLog::record('login', 'User logged in', null, null, $this->admin->id);
        ActivityLog::record('created', 'Article published', null, null, $this->admin->id);

        $filterResponse = $this->actingAs($this->admin)->get(route('admin.activity-logs.index', ['action' => 'login']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('User logged in');

        $exportResponse = $this->actingAs($this->admin)->get(route('admin.activity-logs.export'));
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_purge_activity_logs(): void {
        ActivityLog::record('login', 'Old log entry', null, null, $this->admin->id);

        $response = $this->actingAs($this->admin)->post(route('admin.activity-logs.clear'), [
            'days' => 'all',
        ]);
        $response->assertRedirect();

        // One security log remains documenting the purge
        $this->assertTrue(ActivityLog::where('action', 'security')->exists());
    }

    public function test_admin_can_view_profile_page(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('My Profile & Preferences');
        $response->assertSee('Preferred Timezone');
        $response->assertSee($this->admin->email);
    }

    public function test_admin_can_update_profile_and_timezone(): void {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name'        => 'Updated Director Name',
            'email'       => 'updated.admin@xivig.com',
            'designation' => 'Senior Editor-in-Chief',
            'timezone'    => 'America/New_York',
            'bio'         => 'Luxury editorial curator and critic.',
        ]);

        $response->assertRedirect();
        $this->admin->refresh();

        $this->assertEquals('Updated Director Name', $this->admin->name);
        $this->assertEquals('updated.admin@xivig.com', $this->admin->email);
        $this->assertEquals('America/New_York', $this->admin->timezone);
        $this->assertEquals('Senior Editor-in-Chief', $this->admin->designation);
    }

    public function test_admin_can_upload_profile_avatar(): void {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = UploadedFile::fake()->image('director_avatar.jpg', 300, 300);

        $response = $this->actingAs($this->admin)->postJson(route('admin.profile.avatar'), [
            'avatar' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'avatar_url', 'message']);

        $this->admin->refresh();
        $this->assertNotNull($this->admin->avatar);
        $this->assertStringContainsString('avatar_', $this->admin->avatar);
        $this->assertNotNull($this->admin->avatar_url);
    }

    public function test_admin_profile_update_without_avatar_preserves_existing_avatar(): void {
        $this->admin->update(['avatar' => '/storage/avatars/existing_director.jpg']);

        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name'  => 'New Director Name',
            'email' => $this->admin->email,
        ]);

        $response->assertRedirect();
        $this->admin->refresh();
        $this->assertEquals('/storage/avatars/existing_director.jpg', $this->admin->avatar);
    }

    public function test_admin_can_remove_avatar(): void {
        $this->admin->update(['avatar' => '/storage/avatars/test.jpg']);

        $response = $this->actingAs($this->admin)->delete(route('admin.profile.avatar.remove'));
        $response->assertRedirect();

        $this->admin->refresh();
        $this->assertNull($this->admin->avatar);
        $this->assertNull($this->admin->avatar_url);
    }

    public function test_admin_can_change_password_with_valid_current_password(): void {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.password'), [
            'current_password'      => 'admin123456',
            'password'              => 'new_luxury_pass_2027',
            'password_confirmation' => 'new_luxury_pass_2027',
        ]);

        $response->assertRedirect();
        $this->admin->refresh();
        $this->assertTrue(Hash::check('new_luxury_pass_2027', $this->admin->password));
    }

    public function test_password_change_fails_with_invalid_current_password(): void {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.password'), [
            'current_password'      => 'wrong_password',
            'password'              => 'new_luxury_pass_2027',
            'password_confirmation' => 'new_luxury_pass_2027',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_admin_can_toggle_two_factor(): void {
        $this->assertFalse($this->admin->two_factor_enabled);

        $response = $this->actingAs($this->admin)->post(route('admin.profile.two-factor'));
        $response->assertRedirect();

        $this->admin->refresh();
        $this->assertTrue($this->admin->two_factor_enabled);
    }

    public function test_system_timezone_setting_can_be_updated(): void {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'site_title'           => 'XIVIG Maison Gazette',
            'contact_email'        => 'gazette@xivig.com',
            'posts_per_page'       => 9,
            'comment_auto_approve' => 1,
            'timezone'             => 'Asia/Tokyo',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Asia/Tokyo', Setting::get('timezone'));
    }

    public function test_timezone_helper_formats_dates_correctly(): void {
        $utcTime = now();
        $formatted = TimezoneHelper::format($utcTime, 'Y-m-d');
        $this->assertNotEmpty($formatted);

        $this->admin->timezone = 'America/New_York';
        $formattedNY = TimezoneHelper::format($utcTime, 'H:i', $this->admin);
        $this->assertNotEmpty($formattedNY);
    }

    public function test_admin_can_view_live_chat_and_fetch_messages(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.chat.index'));
        $response->assertStatus(200);
        $response->assertSee('Live Staff Chat');
        $response->assertSee('Editorial Salon');

        $fetchResponse = $this->actingAs($this->admin)->getJson(route('admin.chat.fetch', ['channel' => 'editorial_salon']));
        $fetchResponse->assertStatus(200);
        $fetchResponse->assertJsonStructure(['messages', 'last_id']);
    }

    public function test_admin_can_send_chat_message(): void {
        $response = $this->actingAs($this->admin)->postJson(route('admin.chat.send'), [
            'channel' => 'editorial_salon',
            'message' => 'Monograph proofing is complete.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $this->admin->id,
            'channel'   => 'editorial_salon',
            'message'   => 'Monograph proofing is complete.',
        ]);
    }

    public function test_admin_can_view_email_inbox(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.emails.index'));
        $response->assertStatus(200);
        $response->assertSee('Dispatches & Mailbox');
        $response->assertSee('Inbox');
        $response->assertSee('Atelier Dispatch: Spring 2027 Couture Textile Selection');
    }

    public function test_contact_form_submission_goes_only_to_contact_messages_not_mailbox(): void {
        $response = $this->post(route('contact.submit'), [
            'name'    => 'Madame Jacqueline Dupont',
            'email'   => 'jacqueline@dupont-couture.fr',
            'subject' => 'Silk Draping Inquiry',
            'message' => 'We would love to discuss a potential editorial partnership regarding archival embroidery.',
        ]);

        $response->assertRedirect();

        // Must exist in contact_messages
        $this->assertDatabaseHas('contact_messages', [
            'name'    => 'Madame Jacqueline Dupont',
            'email'   => 'jacqueline@dupont-couture.fr',
            'subject' => 'Silk Draping Inquiry',
        ]);

        // Must NOT exist in admin_emails mailbox
        $this->assertDatabaseMissing('admin_emails', [
            'sender_email' => 'jacqueline@dupont-couture.fr',
        ]);
    }

    public function test_admin_can_read_and_star_email(): void {
        $email = AdminEmail::where('folder', 'inbox')->firstOrFail();

        $readResponse = $this->actingAs($this->admin)->get(route('admin.emails.show', $email));
        $readResponse->assertStatus(200);
        $readResponse->assertSee($email->subject);

        $email->refresh();
        $this->assertTrue($email->is_read);

        $starResponse = $this->actingAs($this->admin)->postJson(route('admin.emails.star', $email));
        $starResponse->assertStatus(200);
        $starResponse->assertJsonStructure(['success', 'is_starred']);
    }

    public function test_admin_can_compose_and_send_email(): void {
        $composeResponse = $this->actingAs($this->admin)->get(route('admin.emails.compose'));
        $composeResponse->assertStatus(200);
        $composeResponse->assertSee('Compose New Dispatch');

        $sendResponse = $this->actingAs($this->admin)->post(route('admin.emails.send'), [
            'recipient_email' => 'critic@atelier-paris.fr',
            'recipient_name'  => 'Paris Atelier Desk',
            'subject'         => 'Review of Autumn/Winter Archive',
            'body'            => '<p>We have approved the monograph drafts for publication.</p>',
            'action'          => 'send',
        ]);

        $sendResponse->assertRedirect(route('admin.emails.index', ['folder' => 'sent']));

        $this->assertDatabaseHas('admin_emails', [
            'recipient_email' => 'critic@atelier-paris.fr',
            'subject'         => 'Review of Autumn/Winter Archive',
            'folder'          => 'sent',
        ]);
    }

    public function test_admin_can_move_email_to_trash_and_destroy(): void {
        $email = AdminEmail::create([
            'sender_name'     => 'Test Sender',
            'sender_email'    => 'sender@test.com',
            'recipient_email' => 'admin@xivig.com',
            'subject'         => 'Spam or Deprecated Dispatch',
            'body'            => 'Deprecated message content.',
            'folder'          => 'inbox',
        ]);

        $trashResponse = $this->actingAs($this->admin)->post(route('admin.emails.trash', $email));
        $trashResponse->assertRedirect();

        $email->refresh();
        $this->assertEquals('trash', $email->folder);

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.emails.destroy', $email));
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('admin_emails', ['id' => $email->id]);
    }

    public function test_admin_can_view_contact_inquiries(): void {
        ContactMessage::create([
            'name'       => 'Baroness Sophia',
            'email'      => 'sophia@haute-horlogerie.ch',
            'subject'    => 'Tourbillon Exhibition Invitation',
            'message'    => 'We would be honored to host the Maison XIVIG critic.',
            'is_read'    => false,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.contacts.index'));
        $response->assertStatus(200);
        $response->assertSee('Patron Inquiries');
        $response->assertSee('Baroness Sophia');
        $response->assertSee('Tourbillon Exhibition Invitation');
    }

    public function test_admin_can_view_and_read_inquiry_details(): void {
        $inquiry = ContactMessage::create([
            'name'       => 'Count Henri',
            'email'      => 'henri@atelier.fr',
            'subject'    => 'Bespoke Tailoring Archive',
            'message'    => 'Seeking high-resolution scans of the 1920s embroidery.',
            'is_read'    => false,
            'ip_address' => '192.168.1.10',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.contacts.show', $inquiry));
        $response->assertStatus(200);
        $response->assertSee('Inquiry from Count Henri');
        $response->assertSee('Seeking high-resolution scans of the 1920s embroidery.');

        $inquiry->refresh();
        $this->assertTrue($inquiry->is_read);
    }

    public function test_admin_can_reply_to_contact_inquiry(): void {
        $inquiry = ContactMessage::create([
            'name'       => 'Viscount Julian',
            'email'      => 'julian@couture.com',
            'subject'    => 'Runway Feature Request',
            'message'    => 'Requesting press credentials for Paris Fashion Week.',
            'is_read'    => true,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.contacts.reply', $inquiry), [
            'reply_subject' => 'Re: Runway Feature Request',
            'reply_body'    => '<p>Your accreditation has been officially sanctioned by the desk.</p>',
        ]);

        $response->assertRedirect(route('admin.contacts.show', $inquiry));

        $this->assertDatabaseHas('admin_emails', [
            'recipient_email' => 'julian@couture.com',
            'subject'         => 'Re: Runway Feature Request',
            'folder'          => 'sent',
        ]);
    }

    public function test_admin_can_toggle_inquiry_read_status(): void {
        $inquiry = ContactMessage::create([
            'name'       => 'Lady Clara',
            'email'      => 'clara@salon.co.uk',
            'subject'    => 'General Query',
            'message'    => 'Inquiring regarding subscription tiers.',
            'is_read'    => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.contacts.toggle-read', $inquiry));
        $response->assertRedirect();

        $inquiry->refresh();
        $this->assertFalse($inquiry->is_read);
    }

    public function test_admin_can_bulk_action_on_inquiries(): void {
        $i1 = ContactMessage::create(['name' => 'User One', 'email' => 'u1@test.com', 'message' => 'Message 1', 'is_read' => false]);
        $i2 = ContactMessage::create(['name' => 'User Two', 'email' => 'u2@test.com', 'message' => 'Message 2', 'is_read' => false]);

        $markReadResponse = $this->actingAs($this->admin)->post(route('admin.contacts.bulk'), [
            'action' => 'mark_read',
            'ids'    => [$i1->id, $i2->id],
        ]);
        $markReadResponse->assertRedirect();

        $this->assertTrue($i1->fresh()->is_read);
        $this->assertTrue($i2->fresh()->is_read);

        $deleteResponse = $this->actingAs($this->admin)->post(route('admin.contacts.bulk'), [
            'action' => 'delete',
            'ids'    => [$i1->id],
        ]);
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('contact_messages', ['id' => $i1->id]);
        $this->assertDatabaseHas('contact_messages', ['id' => $i2->id]);
    }

    public function test_contact_page_generates_arithmetic_captcha_in_session(): void {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
        $response->assertSessionHas('contact_captcha_num1');
        $response->assertSessionHas('contact_captcha_num2');
        $response->assertSessionHas('contact_captcha_answer');
        $response->assertSee('Security Verification');
    }

    public function test_contact_submission_fails_with_invalid_captcha(): void {
        $this->withSession([
            'contact_captcha_num1'   => 5,
            'contact_captcha_num2'   => 4,
            'contact_captcha_answer' => 9,
        ]);

        $response = $this->from(route('contact'))->post(route('contact.submit'), [
            'name'    => 'Spam Bot',
            'email'   => 'bot@spammer.com',
            'subject' => 'Spam dispatch',
            'message' => 'This spam message should be rejected by the security challenge.',
            'captcha' => 999, // Incorrect answer
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['captcha']);
        $this->assertDatabaseMissing('contact_messages', ['email' => 'bot@spammer.com']);
    }

    public function test_contact_submission_succeeds_with_valid_captcha(): void {
        $this->withSession([
            'contact_captcha_num1'   => 6,
            'contact_captcha_num2'   => 3,
            'contact_captcha_answer' => 9,
        ]);

        $response = $this->from(route('contact'))->post(route('contact.submit'), [
            'name'    => 'Duchess Beatrice',
            'email'   => 'beatrice@palais.mc',
            'subject' => 'Monaco Salon Inquiry',
            'message' => 'We welcome the editorial team for the upcoming Monaco horology gala.',
            'captcha' => 9, // Correct answer
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('contact_success');
        $this->assertDatabaseHas('contact_messages', [
            'email'   => 'beatrice@palais.mc',
            'subject' => 'Monaco Salon Inquiry',
        ]);
        $this->assertDatabaseMissing('admin_emails', [
            'sender_email' => 'beatrice@palais.mc',
        ]);
    }

    public function test_captcha_refresh_endpoint_returns_json_and_updates_session(): void {
        $response = $this->getJson(route('contact.captcha.refresh'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'num1', 'num2', 'question']);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals($data['num1'] + $data['num2'], session('contact_captcha_answer'));
    }
}
