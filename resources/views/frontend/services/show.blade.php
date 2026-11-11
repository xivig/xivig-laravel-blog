@extends('frontend.layouts.app')

@section('title', $service->title . ' | XIVIG Maison Gazette Atelier')
@section('meta_description', $service->tagline ?: Str::limit(strip_tags($service->description), 150))

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb extra-small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-muted text-decoration-none">Bespoke Services</a></li>
            <li class="breadcrumb-item active text-body fw-bold" aria-current="page">{{ $service->title }}</li>
        </ol>
    </nav>

    <!-- Flash Confirmation -->
    @if(session('commission_success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center gap-3">
        <i class="bi bi-check-circle-fill fs-3 text-success"></i>
        <div>
            <h5 class="fw-bold text-success mb-1">Commission Brief Received</h5>
            <p class="mb-0 text-muted small">{{ session('commission_success') }}</p>
        </div>
    </div>
    @endif

    <div class="row g-5">
        <!-- Main Service Dossier -->
        <div class="col-lg-8">
            <div class="p-4 p-md-5 rounded-4 bg-body border shadow-sm mb-5 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <span class="badge bg-body-secondary text-body rounded-pill px-3 py-1.5 extra-small fw-bold text-uppercase letter-spacing-1">
                        {{ $service->category }}
                    </span>
                    @if($service->badge)
                    <span class="badge rounded-pill px-3 py-1.5 extra-small fw-bold shadow-sm" style="background: linear-gradient(135deg, #c9a96e, #967d46); color: #000;">
                        <i class="bi bi-star-fill me-1"></i>{{ $service->badge }}
                    </span>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                         style="width: 60px; height: 60px; background: rgba(201, 169, 110, 0.15); border: 2px solid rgba(201, 169, 110, 0.35); color: #c9a96e;">
                        <i class="bi {{ $service->icon ?: 'bi-gem' }} fs-3"></i>
                    </div>
                    <h1 class="display-6 fw-black text-body mb-0 font-cinzel">{{ $service->title }}</h1>
                </div>

                @if($service->tagline)
                <p class="lead text-muted mb-4 fst-italic" style="font-size: 1.15rem;">
                    "{{ $service->tagline }}"
                </p>
                @endif

                <div class="p-3 rounded-3 bg-body-secondary mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold d-block">Investment Model</span>
                        <strong class="fs-4" style="color: #c9a96e !important;">{{ $service->formatted_price }}</strong>
                    </div>
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold d-block">Estimated Turnaround</span>
                        <span class="fw-semibold text-body"><i class="bi bi-clock me-1 text-primary"></i>{{ $service->delivery_time ?: 'Upon Agreement' }}</span>
                    </div>
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold d-block">Client Focus</span>
                        <span class="fw-semibold text-body"><i class="bi bi-person-check me-1 text-primary"></i>{{ $service->target_audience ?: 'Global Maisons' }}</span>
                    </div>
                </div>

                <hr class="opacity-10 my-4">

                <!-- Narrative Description -->
                <div class="mb-5">
                    <h4 class="fw-bold text-body mb-3 font-cinzel">Discipline &amp; Creative Scope</h4>
                    <div class="text-body lh-lg" style="font-size: 1.05rem; white-space: pre-wrap;">
                        {!! nl2br(e($service->description)) !!}
                    </div>
                </div>

                <!-- Deliverables & Inclusions -->
                @php $features = $service->features_list; @endphp
                @if(!empty($features))
                <div class="mb-5">
                    <h4 class="fw-bold text-body mb-3 font-cinzel">Comprehensive Inclusions &amp; Deliverables</h4>
                    <div class="row g-3">
                        @foreach($features as $feat)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-body-secondary d-flex align-items-start gap-2 h-100">
                                <i class="bi bi-check-circle-fill flex-shrink-0 mt-1" style="color: #c9a96e;"></i>
                                <span class="small fw-semibold text-body">{{ $feat }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Quality Guarantee -->
                <div class="p-4 rounded-4 bg-body-tertiary border d-flex align-items-center gap-3">
                    <i class="bi bi-shield-check fs-1 text-success flex-shrink-0"></i>
                    <div>
                        <h6 class="fw-bold text-body mb-1">Maison Gazette Non-Disclosure &amp; Quality Guarantee</h6>
                        <p class="text-muted extra-small mb-0">Every commission is executed under strict confidential protocols, with direct oversight by our Editor-in-Chief and lead creative directors.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Commission Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top bg-body mb-4" style="top: 100px; border: 1px solid rgba(201, 169, 110, 0.3) !important;">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-body mb-0 font-cinzel">Commission This Offering</h5>
                    <span class="badge bg-warning-subtle text-warning extra-small rounded-pill">{{ $service->category }}</span>
                </div>

                <p class="text-muted small mb-4">
                    Submit your commission brief directly to initiate curatorial review. We will contact you with a formal scope of work.
                </p>

                <form action="{{ route('services.inquire') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                    <input type="text" name="commission_verify_trap" style="display:none !important;" tabindex="-1" autocomplete="off">

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Your Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Monsieur Henri de Valois" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Direct Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="client@luxury-domain.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Company / Maison</label>
                        <input type="text" name="company" class="form-control rounded-3" placeholder="e.g. Valois Horlogerie">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Target Budget</label>
                            <select name="budget" class="form-select form-select-sm rounded-3">
                                <option value="< $2,500">&lt; $2,500</option>
                                <option value="$2,500 - $7,500" selected>$2,500 - $7,500</option>
                                <option value="$7,500 - $20,000">$7,500 - $20,000</option>
                                <option value="$20,000+">$20,000+</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Timeline</label>
                            <select name="timeline" class="form-select form-select-sm rounded-3">
                                <option value="Immediate">Immediate</option>
                                <option value="1 - 2 Months" selected>1 - 2 Months</option>
                                <option value="Flexible">Flexible</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Project Brief &amp; Context <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control rounded-3" rows="4" placeholder="Detail your project goals, references, and required delivery date..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2.5 shadow-sm">
                        <i class="bi bi-send-fill me-1"></i> Submit Commission Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Related Services -->
    @if($relatedServices->isNotEmpty())
    <div class="mt-5 pt-4 border-top">
        <h4 class="fw-black text-body mb-4 font-cinzel">Complementary Atelier Disciplines</h4>
        <div class="row g-4">
            @foreach($relatedServices as $rel)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-body hover-up" style="border: 1px solid rgba(201, 169, 110, 0.2) !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi {{ $rel->icon ?: 'bi-gem' }} text-primary"></i>
                        <span class="text-muted extra-small text-uppercase fw-bold">{{ $rel->category }}</span>
                    </div>
                    <h5 class="fw-bold text-body mb-2">
                        <a href="{{ route('services.show', $rel->slug) }}" class="text-body text-decoration-none hover-gold">
                            {{ $rel->title }}
                        </a>
                    </h5>
                    <p class="text-muted small mb-3">{{ Str::limit($rel->tagline ?: strip_tags($rel->description), 80) }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                        <strong class="text-primary small">{{ $rel->formatted_price }}</strong>
                        <a href="{{ route('services.show', $rel->slug) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 extra-small fw-bold">
                            Specifications &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
