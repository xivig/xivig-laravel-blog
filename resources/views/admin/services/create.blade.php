@extends('admin.layouts.admin')

@section('title', 'Create Atelier Service Offering')
@section('page_title', 'New Service Offering')
@section('parentTitle', 'Atelier Services')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-black text-body mb-1">Create Bespoke Service Offering</h5>
            <span class="text-muted extra-small">Craft a specialized solution, editorial package, or consulting commission for your clientele.</span>
        </div>
        <a href="{{ route('admin.services.index') }}" class="btn btn-light rounded-pill px-4 btn-sm fw-bold border text-body">
            <i class="bi bi-arrow-left me-1"></i> Back to Catalog
        </a>
    </div>

    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Form Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-body mb-3 pb-2 border-bottom">
                        <i class="bi bi-info-circle text-primary me-2"></i>Service Essentials
                    </h6>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Service Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="inputTitle" value="{{ old('title') }}" class="form-control rounded-3" placeholder="e.g. Haute Horlogerie &amp; Luxury Feature Writing" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Discipline / Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" id="inputCategory" list="categoriesList" value="{{ old('category', 'Editorial & Press') }}" class="form-control rounded-3" placeholder="Select or type category..." required>
                            <datalist id="categoriesList">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Custom Slug (Optional)</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" class="form-control rounded-3" placeholder="auto-generated-from-title">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Catchy Tagline / Proposition</label>
                        <input type="text" name="tagline" id="inputTagline" value="{{ old('tagline') }}" class="form-control rounded-3" placeholder="e.g. Authoritative investigative journalism and museum-grade chronicles for prestige maisons.">
                    </div>

                    <div class="mb-0">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Narrative Description</label>
                        <textarea name="description" id="inputDescription" class="form-control rounded-3" rows="6" placeholder="Describe the process, craftsmanship, philosophy, and impact of this bespoke service...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-body mb-3 pb-2 border-bottom">
                        <i class="bi bi-currency-dollar text-success me-2"></i>Pricing &amp; Delivery Framework
                    </h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Pricing Model <span class="text-danger">*</span></label>
                            <select name="pricing_type" id="inputPricingType" class="form-select rounded-3" required>
                                <option value="fixed" {{ old('pricing_type') === 'fixed' ? 'selected' : '' }}>Fixed Fee ($)</option>
                                <option value="starting_at" {{ old('pricing_type') === 'starting_at' ? 'selected' : '' }}>Starting At ($)</option>
                                <option value="retainer" {{ old('pricing_type') === 'retainer' ? 'selected' : '' }}>Monthly Retainer</option>
                                <option value="hourly" {{ old('pricing_type') === 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                <option value="quote" {{ old('pricing_type') === 'quote' ? 'selected' : '' }}>Bespoke Quote Only</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Base Price (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="price" id="inputPrice" value="{{ old('price', 1500) }}" class="form-control" placeholder="1500.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Price Unit / Scope</label>
                            <input type="text" name="price_unit" id="inputPriceUnit" value="{{ old('price_unit', '/feature') }}" class="form-control rounded-3" placeholder="e.g. /feature, /project">
                        </div>
                    </div>

                    <div class="row g-3 mb-0">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Turnaround / Timeline</label>
                            <input type="text" name="delivery_time" id="inputDelivery" value="{{ old('delivery_time', '7 - 10 Business Days') }}" class="form-control rounded-3" placeholder="e.g. 2 - 3 Weeks, Ongoing">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Target Audience / Ideal Client</label>
                            <input type="text" name="target_audience" value="{{ old('target_audience') }}" class="form-control rounded-3" placeholder="e.g. Independent Watchmakers, Luxury Maisons">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold text-body mb-3 pb-2 border-bottom">
                        <i class="bi bi-check2-square text-primary me-2"></i>Deliverables &amp; Inclusions
                    </h6>
                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Key Deliverables (One per line)</label>
                        <textarea name="features" id="inputFeatures" class="form-control rounded-3" rows="5" placeholder="Deep investigative essay (2,500 - 4,000 words)&#10;Bespoke editorial photography curation&#10;Syndicated front-page publication &amp; newsletter feature&#10;Perpetual syndication &amp; marketing license&#10;Private print-ready editorial dossier">{{ old('features') }}</textarea>
                        <span class="text-muted extra-small">Each line becomes an interactive checklist item in the public showcase.</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Live Preview -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-body mb-3 pb-2 border-bottom">
                        <i class="bi bi-sliders text-primary me-2"></i>Publishing Controls
                    </h6>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Showcase Status</label>
                        <select name="status" class="form-select rounded-3" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Publicly Listed)</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Special Badge / Ribbon</label>
                        <input type="text" name="badge" id="inputBadge" value="{{ old('badge', 'Signature Atelier') }}" class="form-control rounded-3" placeholder="e.g. Signature Atelier, Most Requested">
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Service Icon (Bootstrap Icons)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i id="iconPreview" class="bi bi-gem"></i></span>
                            <input type="text" name="icon" id="inputIcon" value="{{ old('icon', 'bi-gem') }}" class="form-control" placeholder="bi-gem">
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 extra-small icon-preset" data-icon="bi-gem"><i class="bi bi-gem me-1"></i>Gem</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 extra-small icon-preset" data-icon="bi-pen-fill"><i class="bi bi-pen-fill me-1"></i>Pen</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 extra-small icon-preset" data-icon="bi-palette-fill"><i class="bi bi-palette-fill me-1"></i>Palette</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 extra-small icon-preset" data-icon="bi-code-slash"><i class="bi bi-code-slash me-1"></i>Code</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 extra-small icon-preset" data-icon="bi-shield-check"><i class="bi bi-shield-check me-1"></i>Trust</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Sort Order Priority</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control rounded-3">
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedCheck" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-body small" for="isFeaturedCheck">
                            <i class="bi bi-star-fill text-warning me-1"></i> Mark as Featured Offering
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Publish Service Offering
                    </button>
                </div>

                <!-- Live Card Preview Simulator -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1 mb-2 d-block">
                        <i class="bi bi-eye text-primary me-1"></i> Live Showcase Preview
                    </span>
                    <div class="card border rounded-4 p-3 bg-body shadow-sm position-relative">
                        <span id="previewBadge" class="badge bg-warning-subtle text-warning extra-small rounded-pill position-absolute top-0 end-0 m-3">
                            Signature Atelier
                        </span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary mb-3 shadow-sm"
                             style="width: 44px; height: 44px; background: rgba(201, 169, 110, 0.15); border: 1px solid rgba(201, 169, 110, 0.3);">
                            <i id="cardIconPreview" class="bi bi-gem fs-5"></i>
                        </div>
                        <span id="previewCategory" class="text-muted extra-small text-uppercase fw-bold">Editorial &amp; Press</span>
                        <h6 id="previewTitle" class="fw-bold text-body mt-1 mb-1">Haute Horlogerie &amp; Feature Writing</h6>
                        <p id="previewTagline" class="text-muted extra-small mb-3">Authoritative investigative journalism for prestige maisons.</p>
                        <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted extra-small d-block">Investment</span>
                                <strong id="previewPrice" class="text-primary small">$1,500 /feature</strong>
                            </div>
                            <span class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 extra-small fw-bold">
                                Inquire
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('inputTitle');
    const categoryInput = document.getElementById('inputCategory');
    const taglineInput = document.getElementById('inputTagline');
    const priceInput = document.getElementById('inputPrice');
    const priceUnitInput = document.getElementById('inputPriceUnit');
    const pricingTypeInput = document.getElementById('inputPricingType');
    const badgeInput = document.getElementById('inputBadge');
    const iconInput = document.getElementById('inputIcon');

    const previewTitle = document.getElementById('previewTitle');
    const previewCategory = document.getElementById('previewCategory');
    const previewTagline = document.getElementById('previewTagline');
    const previewPrice = document.getElementById('previewPrice');
    const previewBadge = document.getElementById('previewBadge');
    const cardIconPreview = document.getElementById('cardIconPreview');
    const iconPreview = document.getElementById('iconPreview');

    function updatePreview() {
        if (previewTitle && titleInput) {
            previewTitle.textContent = titleInput.value.trim() || 'Service Title';
        }
        if (previewCategory && categoryInput) {
            previewCategory.textContent = categoryInput.value.trim() || 'Discipline';
        }
        if (previewTagline && taglineInput) {
            previewTagline.textContent = taglineInput.value.trim() || 'Compelling summary of the bespoke offering...';
        }
        if (previewBadge && badgeInput) {
            previewBadge.textContent = badgeInput.value.trim();
            previewBadge.style.display = badgeInput.value.trim() ? 'inline-block' : 'none';
        }
        if (previewPrice) {
            const pType = pricingTypeInput ? pricingTypeInput.value : 'fixed';
            const priceVal = priceInput && priceInput.value ? parseFloat(priceInput.value).toLocaleString() : '0';
            const unitVal = priceUnitInput && priceUnitInput.value ? ' ' + priceUnitInput.value : '';

            if (pType === 'quote') {
                previewPrice.textContent = 'Custom Quote';
            } else if (pType === 'starting_at') {
                previewPrice.textContent = 'From $' + priceVal + unitVal;
            } else if (pType === 'retainer') {
                previewPrice.textContent = '$' + priceVal + ' / mo';
            } else if (pType === 'hourly') {
                previewPrice.textContent = '$' + priceVal + ' / hr';
            } else {
                previewPrice.textContent = '$' + priceVal + unitVal;
            }
        }
        if (iconInput) {
            const iconVal = iconInput.value.trim() || 'bi-gem';
            if (iconPreview) iconPreview.className = 'bi ' + iconVal;
            if (cardIconPreview) cardIconPreview.className = 'bi ' + iconVal + ' fs-5';
        }
    }

    [titleInput, categoryInput, taglineInput, priceInput, priceUnitInput, pricingTypeInput, badgeInput, iconInput].forEach(el => {
        if (el) el.addEventListener('input', updatePreview);
    });

    document.querySelectorAll('.icon-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (iconInput) {
                iconInput.value = this.dataset.icon;
                updatePreview();
            }
        });
    });

    updatePreview();
});
</script>
@endsection
