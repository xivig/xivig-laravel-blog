<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementTest extends TestCase {
    use RefreshDatabase;

    protected User $admin;
    protected Service $service;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->firstOrFail();
        $this->service = Service::firstOrFail();
    }

    public function test_public_services_index_renders_with_catalog_and_modal(): void {
        $response = $this->get(route('services.index'));
        $response->assertStatus(200);
        $response->assertSee('Bespoke Solutions');
        $response->assertSee($this->service->title);
        $response->assertSee('Commission Service');
        $response->assertSee('The Four Pillars of Our Craft');
    }

    public function test_public_services_category_filtering(): void {
        $response = $this->get(route('services.index', ['category' => $this->service->category]));
        $response->assertStatus(200);
        $response->assertSee($this->service->title);
    }

    public function test_public_services_show_renders_detailed_dossier(): void {
        $response = $this->get(route('services.show', $this->service->slug));
        $response->assertStatus(200);
        $response->assertSee($this->service->title);
        $response->assertSee('Commission This Offering');
        $response->assertSee('Maison Gazette Non-Disclosure &amp; Quality Guarantee', false);
    }

    public function test_client_can_submit_service_commission_inquiry(): void {
        $response = $this->post(route('services.inquire'), [
            'name'       => 'Baroness Caroline de Saint-Germain',
            'email'      => 'caroline@saint-germain.fr',
            'company'    => 'Saint-Germain Heritage Group',
            'phone'      => '+33 1 45 20 10 00',
            'service_id' => $this->service->id,
            'budget'     => '$7,500 - $20,000',
            'timeline'   => 'Within 1 - 2 Months',
            'message'    => 'We are commissioning an authoritative history of our 19th century horological workshop for the upcoming biennial retrospective.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('commission_success');

        $this->assertDatabaseHas('service_inquiries', [
            'email'      => 'caroline@saint-germain.fr',
            'name'       => 'Baroness Caroline de Saint-Germain',
            'service_id' => $this->service->id,
            'status'     => 'pending',
        ]);
    }

    public function test_inquiry_honeypot_silently_discards_spambots(): void {
        $response = $this->post(route('services.inquire'), [
            'name'                    => 'Spambot 3000',
            'email'                   => 'spam@botnet.ru',
            'commission_verify_trap'  => 'I am a malicious bot filling hidden fields',
            'message'                 => 'Buy cheap pills online immediately.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('service_inquiries', [
            'email' => 'spam@botnet.ru',
        ]);
    }

    public function test_admin_services_index_renders_catalog_and_metrics(): void {
        $response = $this->actingAs($this->admin)->get(route('admin.services.index'));
        $response->assertStatus(200);
        $response->assertSee('Atelier Services &amp; Offerings', false);
        $response->assertSee('Services Catalog');
        $response->assertSee('Total Services');
        $response->assertSee('Client Inquiries');
        $response->assertSee($this->service->title);
    }

    public function test_admin_can_create_new_service_offering(): void {
        $response = $this->actingAs($this->admin)->post(route('admin.services.store'), [
            'title'           => 'Private Horological Restoration & Appraisal',
            'category'        => 'Horology Advisory',
            'tagline'         => 'Museum-grade historical appraisal and mechanical provenance documentation.',
            'description'     => 'Complete historical assessment and restoration roadmap for heirloom timepieces.',
            'pricing_type'    => 'starting_at',
            'price'           => 4000.00,
            'price_unit'      => '/piece',
            'delivery_time'   => '3 - 4 Weeks',
            'features'        => "Microscopic escapement appraisal\nArchival factory record search\nCertified provenance certificate",
            'is_featured'     => '1',
            'status'          => 'active',
            'icon'            => 'bi-gem',
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('services', [
            'title'        => 'Private Horological Restoration & Appraisal',
            'pricing_type' => 'starting_at',
            'price'        => 4000.00,
            'is_featured'  => true,
            'status'       => 'active',
        ]);
    }

    public function test_admin_can_update_service_offering(): void {
        $response = $this->actingAs($this->admin)->put(route('admin.services.update', $this->service->id), [
            'title'        => 'Updated Title Offering',
            'slug'         => $this->service->slug,
            'category'     => $this->service->category,
            'pricing_type' => 'fixed',
            'price'        => 5500.00,
            'status'       => 'active',
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('services', [
            'id'    => $this->service->id,
            'title' => 'Updated Title Offering',
            'price' => 5500.00,
        ]);
    }

    public function test_admin_can_toggle_featured_and_status(): void {
        $initialFeatured = $this->service->is_featured;

        $response = $this->actingAs($this->admin)->post(route('admin.services.toggle-featured', $this->service->id));
        $response->assertRedirect();
        $this->service->refresh();
        $this->assertEquals(!$initialFeatured, $this->service->is_featured);

        $initialStatus = $this->service->status;
        $response2 = $this->actingAs($this->admin)->post(route('admin.services.toggle-status', $this->service->id));
        $response2->assertRedirect();
        $this->service->refresh();
        $this->assertNotEquals($initialStatus, $this->service->status);
    }

    public function test_admin_can_destroy_service_offering(): void {
        $response = $this->actingAs($this->admin)->delete(route('admin.services.destroy', $this->service->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('services', ['id' => $this->service->id]);
    }

    public function test_admin_services_inquiries_desk_renders_and_updates(): void {
        $inquiry = ServiceInquiry::create([
            'service_id' => $this->service->id,
            'name'       => 'Jean Valjean',
            'email'      => 'valjean@montreuil.fr',
            'message'    => 'Looking for a comprehensive editorial narrative on French manufacturing history.',
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.services.inquiries'));
        $response->assertStatus(200);
        $response->assertSee('Client Inquiries &amp; Commissions', false);
        $response->assertSee('Jean Valjean');
        $response->assertSee('valjean@montreuil.fr');

        // Update inquiry status
        $updateResp = $this->actingAs($this->admin)->put(route('admin.services.inquiries.update', $inquiry->id), [
            'status' => 'contacted',
            'notes'  => 'Director contacted client via confidential telephone call.',
        ]);
        $updateResp->assertRedirect();

        $this->assertDatabaseHas('service_inquiries', [
            'id'     => $inquiry->id,
            'status' => 'contacted',
            'notes'  => 'Director contacted client via confidential telephone call.',
        ]);
    }
}
