@extends('frontend.layouts.app')

@section('title', 'Bespoke Services & Private Commissions | XIVIG Maison Gazette')
@section('meta_description', 'Discover bespoke editorial journalism, haute horlogerie advisory, creative direction, and digital architecture curated by the artisans of XIVIG Maison Gazette.')

@section('content')
<!-- Hero Section -->
<section class="position-relative py-5 overflow-hidden" style="background: radial-gradient(circle at top right, rgba(201, 169, 110, 0.12), transparent 60%);">
    <div class="container py-4 text-center">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill border border-warning-subtle bg-body-secondary mb-3">
            <i class="bi bi-gem text-warning"></i>
            <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted">The Atelier &bull; Bespoke Commissions</span>
        </div>
        <h1 class="display-4 fw-black text-body mb-3 font-cinzel">
            Artisanal Mastery &amp; <span class="text-primary" style="color: #c9a96e !important;">Bespoke Solutions</span>
        </h1>
        <p class="lead text-muted mx-auto mb-4" style="max-width: 720px; font-size: 1.15rem;">
            From museum-grade investigative journalism to haute horlogerie curation, creative brand direction, and digital platform architecture — bespoke solutions tailored for prestigious maisons and distinguished individuals.
        </p>

        <!-- Category Filter Pills -->
        @if($categories->isNotEmpty())
        <div class="d-flex justify-content-center flex-wrap gap-2 mt-4">
            <a href="{{ route('services.index') }}" class="btn btn-sm rounded-pill px-4 fw-bold {{ !request('category') || request('category') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                All Offerings
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('services.index', ['category' => $cat]) }}" class="btn btn-sm rounded-pill px-4 fw-bold {{ request('category') === $cat ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- Flash Confirmation -->
@if(session('commission_success'))
<div class="container mb-4">
    <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 d-flex align-items-center gap-3">
        <div class="rounded-circle bg-success text-white p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="bi bi-check-lg fs-4"></i>
        </div>
        <div>
            <h5 class="fw-bold text-success mb-1">Commission Brief Received</h5>
            <p class="mb-0 text-muted small">{{ session('commission_success') }}</p>
        </div>
    </div>
</div>
@endif

<!-- Services Showcase Grid -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @forelse($services as $service)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative bg-body transition-all hover-up"
                     style="border: 1px solid rgba(201, 169, 110, 0.2) !important; transition: transform 0.25s ease, box-shadow 0.25s ease;">
                    
                    @if($service->badge)
                    <span class="badge position-absolute top-0 end-0 m-4 px-3 py-1.5 rounded-pill extra-small fw-bold shadow-sm"
                          style="background: linear-gradient(135deg, #c9a96e, #967d46); color: #000;">
                        <i class="bi bi-star-fill me-1"></i>{{ $service->badge }}
                    </span>
                    @endif

                    <!-- Service Icon -->
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-4 shadow-sm"
                         style="width: 58px; height: 58px; background: rgba(201, 169, 110, 0.15); border: 2px solid rgba(201, 169, 110, 0.35); color: #c9a96e;">
                        <i class="bi {{ $service->icon ?: 'bi-gem' }} fs-3"></i>
                    </div>

                    <!-- Category & Title -->
                    <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1 d-block mb-1">
                        {{ $service->category }}
                    </span>
                    <h4 class="fw-bold text-body mb-2 font-cinzel">
                        <a href="{{ route('services.show', $service->slug) }}" class="text-body text-decoration-none hover-gold">
                            {{ $service->title }}
                        </a>
                    </h4>
                    
                    <!-- Tagline -->
                    <p class="text-muted small mb-4" style="min-height: 42px;">
                        {{ $service->tagline ?: Str::limit(strip_tags($service->description), 90) }}
                    </p>

                    <!-- Investment & Timeline Bar -->
                    <div class="p-3 rounded-3 bg-body-secondary mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted extra-small d-block text-uppercase fw-bold">Investment</span>
                            <strong class="text-body fs-5" style="color: #c9a96e !important;">{{ $service->formatted_price }}</strong>
                        </div>
                        <div class="text-end">
                            <span class="text-muted extra-small d-block text-uppercase fw-bold">Turnaround</span>
                            <span class="small text-muted fw-semibold"><i class="bi bi-clock me-1"></i>{{ $service->delivery_time ?: 'Bespoke' }}</span>
                        </div>
                    </div>

                    <!-- Deliverables Checklist -->
                    @php $features = $service->features_list; @endphp
                    @if(!empty($features))
                    <div class="mb-4 flex-grow-1">
                        <span class="extra-small fw-bold text-uppercase text-muted letter-spacing-1 d-block mb-2">Key Inclusions:</span>
                        <ul class="list-unstyled mb-0 space-y-2">
                            @foreach(array_slice($features, 0, 4) as $feat)
                            <li class="small text-body d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-check-circle-fill flex-shrink-0 mt-1" style="color: #c9a96e; font-size: 0.85rem;"></i>
                                <span>{{ $feat }}</span>
                            </li>
                            @endforeach
                            @if(count($features) > 4)
                            <li class="extra-small text-muted fst-italic">
                                + {{ count($features) - 4 }} additional bespoke inclusions
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 pt-3 border-top mt-auto">
                        <button type="button" class="btn btn-primary rounded-pill px-3 py-2 btn-sm fw-bold flex-grow-1 open-commission-modal"
                                data-id="{{ $service->id }}"
                                data-title="{{ $service->title }}"
                                data-price="{{ $service->formatted_price }}">
                            <i class="bi bi-chat-quote-fill me-1"></i> Commission Service
                        </button>
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm fw-bold" title="View Full Specifications">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="py-5">
                    <i class="bi bi-briefcase fs-1 text-muted opacity-50 d-block mb-3"></i>
                    <h4 class="fw-bold text-body">No Services Currently Listed</h4>
                    <p class="text-muted small">The Atelier is curating new bespoke offerings. Please return shortly.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Methodology & Trust Section -->
<section class="py-5 bg-body-secondary border-top border-bottom">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 extra-small fw-bold text-uppercase letter-spacing-1 mb-2">
                The Atelier Discipline
            </span>
            <h2 class="fw-black text-body font-cinzel">The Four Pillars of Our Craft</h2>
            <p class="text-muted small mx-auto" style="max-width: 600px;">
                Every commission undergoes a rigorous artisanal workflow designed to deliver enduring prestige and intellectual depth.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 col-sm-6 text-center">
                <div class="p-4 rounded-4 bg-body h-100 shadow-sm border">
                    <div class="display-6 fw-black mb-3" style="color: #c9a96e;">01</div>
                    <h5 class="fw-bold text-body mb-2">Curatorial Briefing</h5>
                    <p class="text-muted small mb-0">Deep alignment on your maison's heritage, narrative boundaries, strategic objectives, and tone of voice.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 text-center">
                <div class="p-4 rounded-4 bg-body h-100 shadow-sm border">
                    <div class="display-6 fw-black mb-3" style="color: #c9a96e;">02</div>
                    <h5 class="fw-bold text-body mb-2">Haute Conception</h5>
                    <p class="text-muted small mb-0">Historical research, visual moodboards, technical specifications, and architectural frameworks.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 text-center">
                <div class="p-4 rounded-4 bg-body h-100 shadow-sm border">
                    <div class="display-6 fw-black mb-3" style="color: #c9a96e;">03</div>
                    <h5 class="fw-bold text-body mb-2">Artisanal Execution</h5>
                    <p class="text-muted small mb-0">Impeccable drafting, bespoke photography curation, digital coding, and rigorous editorial vetting.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 text-center">
                <div class="p-4 rounded-4 bg-body h-100 shadow-sm border">
                    <div class="display-6 fw-black mb-3" style="color: #c9a96e;">04</div>
                    <h5 class="fw-bold text-body mb-2">Museum-Grade Delivery</h5>
                    <p class="text-muted small mb-0">Final delivery in multiple high-fidelity formats, permanent archive storage, and worldwide syndication.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Direct Commission Consultation Banner -->
<section class="py-5 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); color: #fff;">
    <div class="container py-4">
        <h2 class="display-6 fw-bold mb-3 font-cinzel text-white">Have a Unique Requirement?</h2>
        <p class="lead text-light opacity-75 mx-auto mb-4" style="max-width: 650px;">
            The Atelier accommodates bespoke commissions that span multidisciplinary fields. Speak directly with our editorial and creative directors.
        </p>
        <button type="button" class="btn rounded-pill px-5 py-3 fw-bold shadow open-commission-modal"
                style="background: #c9a96e; color: #000;"
                data-id=""
                data-title="Custom Tailored Commission"
                data-price="Bespoke">
            <i class="bi bi-pen-fill me-2"></i> Request Private Commission Brief
        </button>
    </div>
</section>

<!-- MODAL: Commission Service Request -->
<div class="modal fade" id="commissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-body-secondary">
                <div>
                    <h5 class="modal-title fw-bold text-body"><i class="bi bi-pen text-primary me-2"></i>Commission Brief Submission</h5>
                    <span class="text-muted extra-small">Connect directly with the XIVIG Gazette Atelier directors.</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('services.inquire') }}" method="POST">
                @csrf
                <!-- Honeypot -->
                <input type="text" name="commission_verify_trap" style="display:none !important;" tabindex="-1" autocomplete="off">

                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 bg-body-secondary mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted extra-small text-uppercase fw-bold d-block">Requested Offering</span>
                            <h6 class="fw-bold text-body mb-0" id="modalOfferingTitle">General Bespoke Commission</h6>
                        </div>
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1.5 fw-bold" id="modalOfferingPrice">
                            Bespoke
                        </span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Your Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Monsieur Henri de Rochefort" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Business / Personal Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="client@prestige-maison.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Maison / Company Name</label>
                            <input type="text" name="company" class="form-control rounded-3" placeholder="e.g. Rochefort Horlogerie">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Contact Telephone (Optional)</label>
                            <input type="tel" name="phone" class="form-control rounded-3" placeholder="+33 1 42 68 00 00">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Associated Service</label>
                            <select name="service_id" id="modalServiceSelect" class="form-select rounded-3">
                                <option value="">General Atelier Inquiry</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->title }} ({{ $svc->formatted_price }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Allocated Budget</label>
                            <select name="budget" class="form-select rounded-3">
                                <option value="< $2,500">&lt; $2,500</option>
                                <option value="$2,500 - $7,500" selected>$2,500 - $7,500</option>
                                <option value="$7,500 - $20,000">$7,500 - $20,000</option>
                                <option value="$20,000+">$20,000+ (Enterprise)</option>
                                <option value="Flexible">To Be Discussed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Desired Timeline</label>
                            <select name="timeline" class="form-select rounded-3">
                                <option value="Immediate">Immediate (&lt; 14 days)</option>
                                <option value="1 - 2 Months" selected>Within 1 - 2 Months</option>
                                <option value="Quarterly">Next Quarter</option>
                                <option value="Flexible">Flexible / Ongoing</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Commission Specifications &amp; Project Brief <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control rounded-3" rows="5" placeholder="Detail your project requirements, scope of work, target audience, and any historical or visual references..." required></textarea>
                        <span class="text-muted extra-small">Our curatorial directors treat all communications with strict non-disclosure discretion.</span>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4 bg-body-tertiary">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-1"></i> Submit Commission Brief
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('commissionModal');
    const modal = new bootstrap.Modal(modalEl);
    const modalTitle = document.getElementById('modalOfferingTitle');
    const modalPrice = document.getElementById('modalOfferingPrice');
    const modalSelect = document.getElementById('modalServiceSelect');

    document.querySelectorAll('.open-commission-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            const svcId = this.dataset.id || '';
            const svcTitle = this.dataset.title || 'General Bespoke Commission';
            const svcPrice = this.dataset.price || 'Bespoke';

            modalTitle.textContent = svcTitle;
            modalPrice.textContent = svcPrice;
            if (modalSelect) modalSelect.value = svcId;

            modal.show();
        });
    });
});
</script>
@endsection
