<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\MediaAsset;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MonetizationAndExtensionsTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    public function test_admin_can_view_gallery_management(): void {
        $response = $this->actingAs($this->admin)->get('/admin/galleries');
        $response->assertStatus(200);
        $response->assertSee('Gallery Management');
        $response->assertSee('Create New Gallery');
    }

    public function test_admin_can_create_and_trash_gallery(): void {
        $response = $this->actingAs($this->admin)->post('/admin/galleries', [
            'title'       => 'Parisian Haute Couture Showcase',
            'description' => 'Behind the scenes at Paris Fashion Week.',
            'status'      => 'active',
        ]);
        $response->assertRedirect();

        $gallery = Gallery::where('title', 'Parisian Haute Couture Showcase')->first();
        $this->assertNotNull($gallery);

        // Move to trash
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/galleries/{$gallery->id}");
        $deleteResponse->assertRedirect();
        $this->assertSoftDeleted('galleries', ['id' => $gallery->id]);

        // Restore
        $restoreResponse = $this->actingAs($this->admin)->post("/admin/galleries/{$gallery->id}/restore");
        $restoreResponse->assertRedirect();
        $this->assertDatabaseHas('galleries', ['id' => $gallery->id, 'deleted_at' => null]);
    }

    public function test_admin_can_inspect_gallery_and_manage_items(): void {
        $gallery = Gallery::create([
            'title'       => 'Venetian Biennale Showcase',
            'slug'        => 'venetian-biennale-showcase',
            'description' => 'Architectural pavilions and installations.',
            'status'      => 'active',
        ]);

        $item1 = GalleryItem::create([
            'gallery_id' => $gallery->id,
            'image_path' => '/storage/galleries/test_photo_1.jpg',
            'title'      => 'Pavilion Entrance',
            'order'      => 1,
        ]);

        $item2 = GalleryItem::create([
            'gallery_id' => $gallery->id,
            'image_path' => '/storage/galleries/test_photo_2.jpg',
            'title'      => 'Sculpture Detail',
            'order'      => 2,
        ]);

        // 1. Visit show page
        $response = $this->actingAs($this->admin)->get("/admin/galleries/{$gallery->id}");
        $response->assertStatus(200);
        $response->assertSee('Venetian Biennale Showcase');
        $response->assertSee('Curated Photographs');
        $response->assertSee('Pavilion Entrance');
        $response->assertSee('Sculpture Detail');

        // 2. Set cover photo
        $coverResponse = $this->actingAs($this->admin)->post("/admin/galleries/{$gallery->id}/items/{$item2->id}/cover");
        $coverResponse->assertRedirect();
        $this->assertEquals($item2->image_path, $gallery->fresh()->cover_image);

        // 3. Delete single image
        $deleteItemResponse = $this->actingAs($this->admin)->delete("/admin/galleries/{$gallery->id}/items/{$item1->id}");
        $deleteItemResponse->assertRedirect();
        $this->assertNull(GalleryItem::find($item1->id));
        $this->assertEquals(1, $gallery->items()->count());
    }

    public function test_admin_can_view_ads_manager_and_launch_campaign(): void {
        $response = $this->actingAs($this->admin)->get('/admin/ads');
        $response->assertStatus(200);
        $response->assertSee('Ads Manager');
        $response->assertSee('Sponsored Content');
        $response->assertSee('New Campaign');

        $createResponse = $this->actingAs($this->admin)->post('/admin/ads', [
            'title'      => 'Bespoke Sartorial Showcase',
            'slot'       => 'sidebar',
            'target_url' => 'https://haute-couture.example.com',
            'is_active'  => 1,
        ]);
        $createResponse->assertRedirect();

        $ad = Advertisement::where('title', 'Bespoke Sartorial Showcase')->first();
        $this->assertNotNull($ad);
        $this->assertTrue($ad->is_active);

        // Toggle ad
        $toggleResponse = $this->actingAs($this->admin)->postJson("/admin/ads/{$ad->id}/toggle");
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true, 'is_active' => false]);
    }

    public function test_admin_can_toggle_rbac_permission_matrix(): void {
        $response = $this->actingAs($this->admin)->get('/admin/roles');
        $response->assertStatus(200);
        $response->assertSee('Elite RBAC Matrix');
        $response->assertSee('PERMISSION AUTHORITY');

        // Toggle permission for author role
        $toggleResponse = $this->actingAs($this->admin)->postJson('/admin/roles/permissions/toggle', [
            'role'       => 'author',
            'permission' => 'publish-posts',
            'enabled'    => 1,
        ]);
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);

        $authorRole = Role::where('name', 'author')->first();
        $this->assertTrue($authorRole->hasPermissionTo('publish_posts'));

        // Toggle off
        $toggleOffResponse = $this->actingAs($this->admin)->postJson('/admin/roles/permissions/toggle', [
            'role'       => 'author',
            'permission' => 'publish-posts',
            'enabled'    => 0,
        ]);
        $toggleOffResponse->assertStatus(200);
        $this->assertFalse($authorRole->fresh()->hasPermissionTo('publish_posts'));
    }

    public function test_admin_can_sync_integrations_and_toggle_maintenance(): void {
        $syncResponse = $this->actingAs($this->admin)->post('/admin/settings/integrations', [
            'google_analytics_id' => 'G-ABC1234567',
            'openai_api_key'      => 'sk-proj-testkey123',
            'mailchimp_api_key'   => 'testmc-us1',
            'mailchimp_list_id'   => 'LIST987',
        ]);
        $syncResponse->assertRedirect();

        $this->assertEquals('G-ABC1234567', Setting::get('google_analytics_id'));
        $this->assertEquals('sk-proj-testkey123', Setting::get('openai_api_key'));

        // Toggle maintenance mode on
        $toggleResponse = $this->actingAs($this->admin)->postJson('/admin/settings/maintenance/toggle', [
            'maintenance_mode' => 1,
        ]);
        $toggleResponse->assertStatus(200);
        $this->assertEquals('1', Setting::get('maintenance_mode'));

        // Guest visitor visits public page -> should receive 503 maintenance page
        auth()->logout();
        $guestResponse = $this->get('/');
        $guestResponse->assertStatus(503);
        $guestResponse->assertSee('Under Architectural Refinement');

        // Turn maintenance off
        Setting::set('maintenance_mode', '0');
        $normalResponse = $this->get('/');
        $normalResponse->assertStatus(200);
    }

    public function test_admin_can_view_and_manage_transactions(): void {
        // 1. Visit empty transactions page matching user screenshot
        $response = $this->actingAs($this->admin)->get('/admin/transactions');
        $response->assertStatus(200);
        $response->assertSee('Transactions');
        $response->assertSee('Transaction History');
        $response->assertSee('Record of all subscriptions and payments.');
        $response->assertSee('No transactions found.');

        // 2. Record new transaction
        $storeResponse = $this->actingAs($this->admin)->post('/admin/transactions', [
            'customer_name'  => 'Lord Sterling Windsor',
            'customer_email' => 'sterling@windsor.uk',
            'amount'         => 299.00,
            'currency'       => 'USD',
            'payment_method' => 'Stripe',
            'status'         => 'completed',
            'description'    => 'Haute Horology Atelier VIP Patronage',
        ]);
        $storeResponse->assertRedirect();

        $transaction = Transaction::where('customer_name', 'Lord Sterling Windsor')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('299.00', $transaction->amount);
        $this->assertEquals('$299.00', $transaction->formatted_amount);

        // 3. Page now displays populated transaction
        $viewResponse = $this->actingAs($this->admin)->get('/admin/transactions');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Lord Sterling Windsor');
        $viewResponse->assertSee('sterling@windsor.uk');
        $viewResponse->assertSee('$299.00');
        $viewResponse->assertSee('Stripe');
        $viewResponse->assertSee('Completed');
        $viewResponse->assertDontSee('No transactions found.');

        // 4. Update status
        $updateResponse = $this->actingAs($this->admin)->put("/admin/transactions/{$transaction->id}", [
            'status'      => 'refunded',
            'description' => 'Patron requested refund',
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals('refunded', $transaction->fresh()->status);

        // 5. Delete transaction
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/transactions/{$transaction->id}");
        $deleteResponse->assertRedirect();
        $this->assertNull(Transaction::find($transaction->id));
    }

    public function test_admin_can_view_and_manage_media_assets(): void {
        Storage::fake('public');

        // 1. Visit media vault
        $response = $this->actingAs($this->admin)->get('/admin/media');
        $response->assertStatus(200);
        $response->assertSee('Media Assets Vault');
        $response->assertSee('Upload Digital Media');
        $response->assertSee('Curated Media Assets');

        // 2. Upload test image asset
        $fakeImage = UploadedFile::fake()->image('atelier_cover.jpg', 1920, 1080);
        $uploadResponse = $this->actingAs($this->admin)->post('/admin/media/upload', [
            'file'     => $fakeImage,
            'alt_text' => 'Atelier Luxury Facade',
        ]);
        $uploadResponse->assertRedirect();

        $asset = MediaAsset::where('alt_text', 'Atelier Luxury Facade')->first();
        $this->assertNotNull($asset);
        $this->assertEquals('atelier_cover.jpg', $asset->original_name);
        $this->assertEquals('1920x1080', $asset->dimensions);
        $this->assertStringContainsString('/storage/', $asset->url);

        // 3. Page shows the asset
        $pageResponse = $this->actingAs($this->admin)->get('/admin/media');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('atelier_cover.jpg');

        // 4. Soft delete asset
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/media/{$asset->id}");
        $deleteResponse->assertRedirect();
        $this->assertNull(MediaAsset::find($asset->id));
        $this->assertNotNull(MediaAsset::withTrashed()->find($asset->id));

        // 5. Check trash tab
        $trashResponse = $this->actingAs($this->admin)->get('/admin/media?tab=trash');
        $trashResponse->assertStatus(200);
        $trashResponse->assertSee('atelier_cover.jpg');

        // 6. Restore asset
        $restoreResponse = $this->actingAs($this->admin)->post("/admin/media/{$asset->id}/restore");
        $restoreResponse->assertRedirect();
        $this->assertNotNull(MediaAsset::find($asset->id));

        // 7. Bulk soft delete & Bulk force delete
        $asset2 = MediaAsset::create([
            'original_name' => 'bulk_sample.jpg',
            'file_name'     => 'sample.jpg',
            'file_path'     => 'media/sample.jpg',
            'file_size'     => 1024,
            'mime_type'     => 'image/jpeg',
        ]);

        $bulkDestroyResponse = $this->actingAs($this->admin)->post('/admin/media/bulk-destroy', [
            'ids' => [$asset->id, $asset2->id],
        ]);
        $bulkDestroyResponse->assertRedirect();
        $this->assertNull(MediaAsset::find($asset->id));
        $this->assertNull(MediaAsset::find($asset2->id));

        // Bulk Restore
        $bulkRestoreResponse = $this->actingAs($this->admin)->post('/admin/media/bulk-restore', [
            'ids' => [$asset->id],
        ]);
        $bulkRestoreResponse->assertRedirect();
        $this->assertNotNull(MediaAsset::find($asset->id));

        // Bulk Force Delete
        $bulkPurgeResponse = $this->actingAs($this->admin)->post('/admin/media/bulk-force-delete', [
            'ids' => [$asset2->id],
        ]);
        $bulkPurgeResponse->assertRedirect();
        $this->assertNull(MediaAsset::withTrashed()->find($asset2->id));
    }

    public function test_advertisements_display_on_front_page_and_click_redirects(): void {
        $ad = Advertisement::create([
            'title'       => 'Patek Philippe Celestial Horology',
            'slot'        => 'sidebar',
            'target_url'  => 'https://patek.com/celestial',
            'is_active'   => true,
            'impressions' => 0,
            'clicks'      => 0,
        ]);

        // 1. Visit Front Page
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Patek Philippe Celestial Horology');
        $response->assertSee('Partner Showcase');

        // 2. Impression is counted
        $this->assertGreaterThanOrEqual(1, $ad->fresh()->impressions);

        // 3. Click Tracking Endpoint
        $clickResponse = $this->get(route('ad.click', $ad->id));
        $clickResponse->assertRedirect('https://patek.com/celestial');
        $this->assertEquals(1, $ad->fresh()->clicks);
    }

    public function test_author_avatar_renders_without_broken_paths_and_with_fallback(): void {
        $obLevel = ob_get_level();

        // Create an author with an avatar
        $author = \App\Models\Author::first();
        $this->assertNotNull($author);

        // Assert avatar_url doesn't double prepend storage
        if ($author->avatar_url) {
            $this->assertStringNotContainsString('/storage/storage/', $author->avatar_url);
            $this->assertStringNotContainsString('storage//storage', $author->avatar_url);
        }

        // Visit Front Page and Article Page
        $frontResponse = $this->get('/');
        $frontResponse->assertStatus(200);
        $frontResponse->assertDontSee('/storage//storage');
        $frontResponse->assertDontSee('/storage/storage/');

        $post = \App\Models\Post::published()->where('is_private', false)->first();
        if ($post) {
            $articleResponse = $this->get(route('blog.show', $post->slug));
            $articleResponse->assertStatus(200);
            $articleResponse->assertDontSee('/storage//storage');
            $articleResponse->assertDontSee('/storage/storage/');
        }

        while (ob_get_level() > $obLevel) {
            @ob_end_clean();
        }
    }
}
