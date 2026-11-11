<!-- Footer -->
<footer class="footer py-3 px-4 border-top mt-auto bg-body-tertiary">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2 text-body" style="font-size: 0.875rem;">
            <span class="fw-semibold">&copy; {{ date('Y') }} <strong class="fw-bold text-body">Maison XIVIG</strong></span>
            <span class="text-secondary opacity-50">&bull;</span>
            <span class="text-muted">Haute Couture &amp; Horological Gazette. All rights reserved.</span>
        </div>
        <div class="d-flex align-items-center gap-3" style="font-size: 0.875rem;">
            <span class="badge bg-body text-secondary border px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                <i class="bi bi-shield-check text-success me-1"></i> Security Version: 2.0 (Hardened)
            </span>
            <span class="text-secondary opacity-50">&bull;</span>
            <a href="{{ route('blog.index') }}" target="_blank" class="text-primary fw-semibold text-decoration-none d-inline-flex align-items-center gap-1 hover-gold">
                Public Portal <i class="bi bi-box-arrow-up-right" style="font-size: 0.75rem;"></i>
            </a>
        </div>
    </div>
</footer>
