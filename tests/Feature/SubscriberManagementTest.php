<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberManagementTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    public function test_subscribers_index_renders_with_metrics_and_table(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.subscribers.index'));
        $response->assertStatus(200);
        $response->assertSee('Patron Registry & Subscriptions');
        $response->assertSee('Total Readers');
        $response->assertSee('Active Patrons');
        $response->assertSee('VIP Atelier Patrons');
        $response->assertSee('Enlist Patron');
        $response->assertSee('Batch Import');
        $response->assertSee('Broadcast Chronicle');
    }

    public function test_subscribers_search_and_filters_work(): void {
        NewsletterSubscriber::create([
            'email'  => 'distinctive.vip@geneva.ch',
            'name'   => 'Count of Geneva',
            'tier'   => 'vip',
            'status' => 'subscribed',
        ]);

        $searchResponse = $this->actingAs($this->admin)->get(route('admin.subscribers.index', ['search' => 'distinctive.vip']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('distinctive.vip@geneva.ch');
        $searchResponse->assertSee('Count of Geneva');

        $tierResponse = $this->actingAs($this->admin)->get(route('admin.subscribers.index', ['tier' => 'vip']));
        $tierResponse->assertStatus(200);
        $tierResponse->assertSee('distinctive.vip@geneva.ch');
    }

    public function test_admin_can_store_new_subscriber(): void {
        $response = $this->actingAs($this->admin)->post(route('admin.subscribers.store'), [
            'email'  => 'new.patron@paris-atelier.com',
            'name'   => 'Marquis de Lafayette',
            'tier'   => 'vip',
            'status' => 'subscribed',
            'notes'  => 'Attended 2026 Haute Horlogerie Salon in Geneva.',
        ]);

        $response->assertRedirect(route('admin.subscribers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'new.patron@paris-atelier.com',
            'name'  => 'Marquis de Lafayette',
            'tier'  => 'vip',
        ]);
    }

    public function test_admin_can_update_subscriber(): void {
        $sub = NewsletterSubscriber::create([
            'email'  => 'update.target@domain.fr',
            'name'   => 'Original Name',
            'tier'   => 'standard',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.subscribers.update', $sub->id), [
            'email'  => 'updated.email@domain.fr',
            'name'   => 'Elevated Patron Name',
            'tier'   => 'vip',
            'status' => 'subscribed',
            'notes'  => 'Elevated status after private consultation.',
        ]);

        $response->assertRedirect(route('admin.subscribers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'id'    => $sub->id,
            'email' => 'updated.email@domain.fr',
            'name'  => 'Elevated Patron Name',
            'tier'  => 'vip',
        ]);
    }

    public function test_admin_can_destroy_subscriber(): void {
        $sub = NewsletterSubscriber::create([
            'email'  => 'delete.me@domain.com',
            'status' => 'subscribed',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.subscribers.destroy', $sub->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('newsletter_subscribers', ['id' => $sub->id]);
    }

    public function test_bulk_subscriber_operations(): void {
        $sub1 = NewsletterSubscriber::create(['email' => 'bulk1@domain.com', 'status' => 'subscribed', 'tier' => 'standard']);
        $sub2 = NewsletterSubscriber::create(['email' => 'bulk2@domain.com', 'status' => 'subscribed', 'tier' => 'standard']);

        // Bulk elevate to VIP
        $response = $this->actingAs($this->admin)->post(route('admin.subscribers.bulk'), [
            'ids'    => [$sub1->id, $sub2->id],
            'action' => 'set_vip',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('newsletter_subscribers', ['id' => $sub1->id, 'tier' => 'vip']);
        $this->assertDatabaseHas('newsletter_subscribers', ['id' => $sub2->id, 'tier' => 'vip']);

        // Bulk unsubscribe
        $response2 = $this->actingAs($this->admin)->post(route('admin.subscribers.bulk'), [
            'ids'    => [$sub1->id, $sub2->id],
            'action' => 'unsubscribe',
        ]);

        $response2->assertRedirect();
        $this->assertDatabaseHas('newsletter_subscribers', ['id' => $sub1->id, 'status' => 'unsubscribed']);
        $this->assertDatabaseHas('newsletter_subscribers', ['id' => $sub2->id, 'status' => 'unsubscribed']);
    }

    public function test_export_subscribers_csv(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.subscribers.export'));
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Email', $response->getContent());
    }

    public function test_import_subscribers_from_raw_text(): void {
        $raw = "artisan.curator@florence.it\nDuke of Milan <milan.duke@lombardy.it>";

        $response = $this->actingAs($this->admin)->post(route('admin.subscribers.import'), [
            'raw_emails'  => $raw,
            'import_tier' => 'vip',
        ]);

        $response->assertRedirect(route('admin.subscribers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'artisan.curator@florence.it',
            'tier'  => 'vip',
        ]);
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'milan.duke@lombardy.it',
            'name'  => 'Duke of Milan',
            'tier'  => 'vip',
        ]);
    }

    public function test_broadcast_chronicle_queues_and_records_time(): void {
        $sub = NewsletterSubscriber::create([
            'email'  => 'broadcast.target@domain.ch',
            'status' => 'subscribed',
            'tier'   => 'vip',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.subscribers.broadcast'), [
            'subject'     => 'Volume XV — Architectural Dial Sculpting',
            'body'        => 'Distinguished Patrons, explore our latest exclusive investigation...',
            'target_tier' => 'vip',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $sub->refresh();
        $this->assertNotNull($sub->last_broadcast_at);
    }
}
