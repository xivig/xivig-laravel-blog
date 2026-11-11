<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCrudTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;
    protected User $targetUser;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();

        $this->targetUser = User::create([
            'name'        => 'Julian Vance',
            'email'       => 'julian.vance@xivig.com',
            'password'    => Hash::make('Secret123!'),
            'role'        => 'author',
            'designation' => 'Staff Columnist',
            'phone'       => '+1 555-0199',
            'bio'         => 'Author of Parisian vignettes.',
        ]);
    }

    public function test_user_index_screen_renders_with_metrics_and_user(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('User Accounts & Directory');
        $response->assertSee('Julian Vance');
        $response->assertSee('julian.vance@xivig.com');
        $response->assertSee('Active Directory');
        $response->assertSee('Trash Vault');
    }

    public function test_user_create_screen_renders_successfully(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));
        $response->assertStatus(200);
        $response->assertSee('New User Profile');
        $response->assertSee('Full Name');
        $response->assertSee('Secret Passkey');
    }

    public function test_user_store_creates_account_and_assigns_role(): void {
        $userData = [
            'name'        => 'Aria Montclaire',
            'email'       => 'aria.m@xivig.com',
            'password'    => 'HauteCouture2027!',
            'role'        => 'editor',
            'designation' => 'Couture Editor',
            'phone'       => '+1 555-7788',
            'bio'         => 'Editorial curator of high fashion.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name'  => 'Aria Montclaire',
            'email' => 'aria.m@xivig.com',
            'role'  => 'editor',
        ]);

        $created = User::where('email', 'aria.m@xivig.com')->firstOrFail();
        $this->assertTrue(Hash::check('HauteCouture2027!', $created->password));
    }

    public function test_user_show_details_screen_renders(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $this->targetUser->id));
        $response->assertStatus(200);
        $response->assertSee('Staff & User Profile Dossier');
        $response->assertSee('Julian Vance');
        $response->assertSee('Staff Columnist');
        $response->assertSee('Author of Parisian vignettes.');
    }

    public function test_user_edit_screen_renders_with_current_data(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.users.edit', $this->targetUser->id));
        $response->assertStatus(200);
        $response->assertSee('Edit User Account');
        $response->assertSee('Julian Vance');
        $response->assertSee('julian.vance@xivig.com');
    }

    public function test_user_update_modifies_details(): void {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->targetUser->id), [
            'name'        => 'Julian Vance-Beaufort',
            'email'       => 'julian.vance@xivig.com',
            'role'        => 'editor',
            'designation' => 'Managing Editor',
            'phone'       => '+1 555-9900',
            'bio'         => 'Updated biographical chronicle.',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->targetUser->refresh();
        $this->assertEquals('Julian Vance-Beaufort', $this->targetUser->name);
        $this->assertEquals('editor', $this->targetUser->role);
        $this->assertEquals('Managing Editor', $this->targetUser->designation);
    }

    public function test_user_destroy_moves_user_to_trash(): void {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->targetUser->id));
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $this->targetUser->id]);
    }

    public function test_admin_cannot_delete_own_account(): void {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertNotSoftDeleted('users', ['id' => $this->admin->id]);
    }

    public function test_trashed_users_index_and_restore_flow(): void {
        $this->targetUser->delete();

        // Check trash index
        $trashIndex = $this->actingAs($this->admin)->get(route('admin.users.index', ['status' => 'trash']));
        $trashIndex->assertStatus(200);
        $trashIndex->assertSee('Julian Vance');

        // Restore user
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.users.restore', $this->targetUser->id));
        $restoreResponse->assertRedirect();
        $restoreResponse->assertSessionHas('success');

        $this->assertFalse($this->targetUser->fresh()->trashed());
    }

    public function test_user_force_delete_permanently_removes_record(): void {
        $this->targetUser->delete();

        $forceDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.users.forceDelete', $this->targetUser->id));
        $forceDeleteResponse->assertRedirect();
        $forceDeleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $this->targetUser->id]);
    }

    public function test_user_duplicate_screen_renders_with_clone_data(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.users.duplicate', $this->targetUser->id));
        $response->assertStatus(200);
        $response->assertSee('Duplication Mode Active');
        $response->assertSee('Julian Vance');
        $response->assertSee('copy_');
    }

    public function test_bulk_actions_delete_restore_force_delete(): void {
        $userA = User::create(['name' => 'User A', 'email' => 'a@xivig.com', 'password' => 'secret123', 'role' => 'user']);
        $userB = User::create(['name' => 'User B', 'email' => 'b@xivig.com', 'password' => 'secret123', 'role' => 'user']);

        // Bulk Delete
        $bulkDelete = $this->actingAs($this->admin)->post(route('admin.users.bulk'), [
            'action' => 'delete',
            'ids'    => [$userA->id, $userB->id],
        ]);
        $bulkDelete->assertRedirect();
        $bulkDelete->assertSessionHas('success');
        $this->assertSoftDeleted('users', ['id' => $userA->id]);
        $this->assertSoftDeleted('users', ['id' => $userB->id]);

        // Bulk Restore
        $bulkRestore = $this->actingAs($this->admin)->post(route('admin.users.bulk'), [
            'action' => 'restore',
            'ids'    => [$userA->id, $userB->id],
        ]);
        $bulkRestore->assertRedirect();
        $this->assertFalse($userA->fresh()->trashed());
        $this->assertFalse($userB->fresh()->trashed());

        // Soft delete again before force delete
        $userA->delete();
        $userB->delete();

        // Bulk Force Delete
        $bulkForce = $this->actingAs($this->admin)->post(route('admin.users.bulk'), [
            'action' => 'force_delete',
            'ids'    => [$userA->id, $userB->id],
        ]);
        $bulkForce->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $userA->id]);
        $this->assertDatabaseMissing('users', ['id' => $userB->id]);
    }

    public function test_role_permissions_matrix_and_ajax_updates(): void {
        $matrixResponse = $this->actingAs($this->admin)->get(route('admin.users.roles-matrix'));
        $matrixResponse->assertStatus(200);
        $matrixResponse->assertSee('Direct User Role Assignment');
        $matrixResponse->assertSee('Elite RBAC Authority Matrix');
        $matrixResponse->assertSee('RBAC &amp; Clearance Matrix', false);

        // AJAX update user role
        $roleUpdateResponse = $this->actingAs($this->admin)->postJson(route('admin.users.update-role'), [
            'user_id' => $this->targetUser->id,
            'role'    => 'editor',
        ]);
        $roleUpdateResponse->assertStatus(200);
        $roleUpdateResponse->assertJson(['success' => true]);
        $this->assertEquals('editor', $this->targetUser->fresh()->role);

        // Ensure permission exists and toggle
        $permission = Permission::firstOrCreate(['name' => 'manage_users']);
        $permToggleResponse = $this->actingAs($this->admin)->postJson(route('admin.users.update-permission'), [
            'role'          => 'editor',
            'permission_id' => $permission->id,
            'action'        => 'assign',
        ]);
        $permToggleResponse->assertStatus(200);
        $permToggleResponse->assertJson(['success' => true]);
    }
}
