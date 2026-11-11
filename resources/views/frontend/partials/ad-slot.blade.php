@if(isset($ad) && $ad && $ad->is_active)
    @php
        $layout = $layout ?? 'banner';
        $clickUrl = route('ad.click', $ad->id);
    @endphp

    @if($layout === 'banner')
        {{-- Full-width Luxury Editorial Sponsor Banner --}}
        <div class="maison-ad-banner-wrap my-4 my-md-5">
            <div class="maison-ad-card position-relative overflow-hidden rounded-4 border border-gold-subtle shadow-sm bg-surface">
                <div class="position-absolute top-0 end-0 m-3 z-3">
                    <span class="badge bg-gold text-dark fw-bold text-uppercase px-2 py-1" style="font-size:0.65rem; letter-spacing:0.08em;">
                        <i class="bi bi-stars me-1"></i> Partner Showcase
                    </span>
                </div>

                @if(!empty($ad->custom_code))
                    <div class="p-3">
                        {!! $ad->custom_code !!}
                    </div>
                @elseif($ad->image_url)
                    <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="d-block text-decoration-none group-banner-link">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-7 col-lg-8">
                                <div class="ratio ratio-21x9 ratio-md-16x9 h-100 overflow-hidden">
                                    <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="w-100 h-100 object-fit-cover transition-transform" style="transition: transform 0.5s ease;">
                                </div>
                            </div>
                            <div class="col-md-5 col-lg-4 p-4 p-xl-5 d-flex flex-column justify-content-center">
                                <span class="text-gold text-uppercase fw-bold extra-small letter-spacing-2 mb-2 d-block">Curated Maison Partner</span>
                                <h4 class="fw-bold text-body mb-3 font-serif line-clamp-2">{{ $ad->title }}</h4>
                                <div class="mt-2">
                                    <span class="btn btn-outline-gold rounded-pill px-4 py-2 small fw-semibold d-inline-flex align-items-center gap-2">
                                        <span>Discover Atelier</span>
                                        <i class="bi bi-arrow-up-right"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    {{-- Refined Typographic Sponsor Card (When no image provided) --}}
                    <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="d-block p-4 p-md-5 text-decoration-none text-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <span class="text-gold text-uppercase fw-bold extra-small letter-spacing-2 mb-1 d-block">Exclusive Partner Dispatch</span>
                                <h4 class="fw-bold mb-1 font-serif text-body">{{ $ad->title }}</h4>
                                <span class="text-secondary small">Curated insight presented by our luxury maison partners.</span>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="btn btn-gold rounded-pill px-4 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                                    <span>Explore Feature</span>
                                    <i class="bi bi-arrow-up-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </div>

    @elseif($layout === 'card')
        {{-- In-Feed Native Grid Bento Card (matches .bento-card styling) --}}
        <div class="col-md-6 col-lg-4">
            <article class="bento-card h-100 border border-gold-subtle position-relative d-flex flex-column">
                <div class="position-absolute top-0 start-0 m-3 z-3">
                    <span class="badge bg-gold text-dark fw-bold text-uppercase px-2 py-1 shadow-xs" style="font-size:0.65rem; letter-spacing:0.08em;">
                        ★ Sponsored
                    </span>
                </div>

                @if(!empty($ad->custom_code))
                    <div class="p-3 flex-grow-1">
                        {!! $ad->custom_code !!}
                    </div>
                @elseif($ad->image_url)
                    <div class="bento-img-wrap position-relative">
                        <div class="ratio ratio-16x9">
                            <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <span class="floating-category-pill bg-dark text-gold border border-gold-subtle">
                            Maison Partner
                        </span>
                    </div>
                    <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <div class="d-flex align-items-center gap-2 extra-small text-secondary mb-2">
                                <span class="text-gold fw-bold text-uppercase letter-spacing-1">Sponsored Feature</span>
                            </div>
                            <h5 class="fw-bold mb-2">
                                <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="text-body text-decoration-none hover-gold font-serif">
                                    {{ $ad->title }}
                                </a>
                            </h5>
                            <p class="text-secondary small mb-3">
                                Discover exceptional savoir-faire, limited horological creations, and bespoke artistry curated exclusively for Maison readers.
                            </p>
                        </div>
                        <div class="pt-3 border-top border-subtle d-flex align-items-center justify-content-between extra-small">
                            <span class="fw-semibold text-gold">Official Partner</span>
                            <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="text-gold fw-bold text-decoration-none hover-underline d-inline-flex align-items-center gap-1">
                                <span>Visit Showcase</span>
                                <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-4 d-flex flex-column justify-content-between flex-grow-1 bg-surface rounded-4">
                        <div>
                            <div class="text-gold extra-small text-uppercase fw-bold letter-spacing-1 mb-2">Partner Chronicle</div>
                            <h5 class="fw-bold mb-3 font-serif">
                                <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="text-body text-decoration-none hover-gold">
                                    {{ $ad->title }}
                                </a>
                            </h5>
                            <p class="text-secondary small mb-3">
                                Curated luxury presentation from our esteemed maison partners.
                            </p>
                        </div>
                        <div class="pt-3 border-top border-subtle d-flex align-items-center justify-content-between extra-small">
                            <span class="text-secondary">Sponsored</span>
                            <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="btn btn-sm btn-outline-gold rounded-pill px-3 py-1">
                                Explore <i class="bi bi-arrow-up-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </article>
        </div>

    @elseif($layout === 'sidebar')
        {{-- Editorial Sidebar Ad Card --}}
        <div class="editorial-sidebar-card bento-card p-3 rounded-4 border border-gold-subtle mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-gold text-dark fw-bold text-uppercase px-2 py-1" style="font-size:0.62rem;">
                    ★ Partner Spotlight
                </span>
                <span class="extra-small text-secondary">Sponsored</span>
            </div>

            @if(!empty($ad->custom_code))
                <div class="custom-ad-container">
                    {!! $ad->custom_code !!}
                </div>
            @elseif($ad->image_url)
                <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="d-block text-decoration-none group-sidebar-ad">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden mb-3">
                        <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="w-100 h-100 object-fit-cover">
                    </div>
                    <h6 class="fw-bold text-body mb-2 font-serif hover-gold line-clamp-2">{{ $ad->title }}</h6>
                    <div class="d-flex align-items-center justify-content-between extra-small text-gold fw-semibold">
                        <span>Explore Collection</span>
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </a>
            @else
                <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="d-block text-decoration-none p-2">
                    <h6 class="fw-bold text-body mb-2 font-serif hover-gold">{{ $ad->title }}</h6>
                    <div class="d-flex align-items-center justify-content-between extra-small text-gold fw-semibold">
                        <span>Explore Collection</span>
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </a>
            @endif
        </div>

    @elseif($layout === 'inline')
        {{-- In-Article Breakout Ad Banner --}}
        <div class="article-inline-ad my-4 p-4 rounded-4 border border-gold-subtle bg-surface shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge bg-gold text-dark fw-bold text-uppercase px-2 py-1" style="font-size:0.65rem;">
                    ★ Maison Partner Dispatch
                </span>
                <span class="extra-small text-secondary text-uppercase letter-spacing-1">Advertisement</span>
            </div>

            @if(!empty($ad->custom_code))
                <div>{!! $ad->custom_code !!}</div>
            @elseif($ad->image_url)
                <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="text-decoration-none text-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
                                <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="w-100 h-100 object-fit-cover">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-2 font-serif text-body">{{ $ad->title }}</h5>
                            <p class="text-secondary small mb-3">An exclusive presentation of horology & bespoke artistry for the discerning patron.</p>
                            <span class="btn btn-sm btn-outline-gold rounded-pill px-3 fw-semibold">
                                Visit Atelier <i class="bi bi-arrow-up-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </a>
            @else
                <a href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer sponsored" class="text-decoration-none text-body d-block">
                    <h5 class="fw-bold mb-2 font-serif">{{ $ad->title }}</h5>
                    <p class="text-secondary small mb-3">Exclusive partner presentation from our atelier network.</p>
                    <span class="btn btn-sm btn-gold rounded-pill px-3 fw-semibold">
                        Discover More <i class="bi bi-arrow-up-right ms-1"></i>
                    </span>
                </a>
            @endif
        </div>
    @endif
@endif
