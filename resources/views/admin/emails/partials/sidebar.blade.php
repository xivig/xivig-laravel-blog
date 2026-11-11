<div class="card border-0 shadow-sm rounded-4 p-3 bg-body mb-4">
    <!-- Compose Button -->
    <a href="{{ route('admin.emails.compose') }}" class="btn btn-primary rounded-pill w-100 py-2.5 fw-bold shadow-primary d-flex align-items-center justify-content-center gap-2 mb-4" style="font-size: 0.9375rem;">
        <i class="bi bi-pencil-square fs-6"></i>
        <span>Compose Dispatch</span>
    </a>

    <!-- Folders List -->
    <div class="fw-bold text-muted text-uppercase px-2 mb-2" style="font-size: 0.78rem; letter-spacing: 0.08em;">
        MAILBOX FOLDERS
    </div>

    <ul class="nav nav-pills flex-column gap-1 mb-4">
        <li class="nav-item">
            <a href="{{ route('admin.emails.index', ['folder' => 'inbox']) }}" class="nav-link rounded-3 px-3 py-2.5 d-flex align-items-center justify-content-between {{ ($folder ?? 'inbox') === 'inbox' ? 'active fw-bold' : 'text-body hover-bg-body-tertiary' }}" style="font-size: 0.9375rem;">
                <div class="d-flex align-items-center gap-2.5">
                    <i class="bi bi-inbox-fill text-primary fs-6"></i>
                    <span>Inbox</span>
                </div>
                @if(($counts['inbox'] ?? 0) > 0)
                    <span class="badge rounded-pill bg-primary px-2 py-0.5" style="font-size: 0.78rem;">{{ $counts['inbox'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.emails.index', ['folder' => 'starred']) }}" class="nav-link rounded-3 px-3 py-2.5 d-flex align-items-center justify-content-between {{ ($folder ?? '') === 'starred' ? 'active fw-bold' : 'text-body hover-bg-body-tertiary' }}" style="font-size: 0.9375rem;">
                <div class="d-flex align-items-center gap-2.5">
                    <i class="bi bi-star-fill text-warning fs-6"></i>
                    <span>Starred</span>
                </div>
                @if(($counts['starred'] ?? 0) > 0)
                    <span class="badge rounded-pill bg-warning text-dark px-2 py-0.5" style="font-size: 0.78rem;">{{ $counts['starred'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.emails.index', ['folder' => 'sent']) }}" class="nav-link rounded-3 px-3 py-2.5 d-flex align-items-center justify-content-between {{ ($folder ?? '') === 'sent' ? 'active fw-bold' : 'text-body hover-bg-body-tertiary' }}" style="font-size: 0.9375rem;">
                <div class="d-flex align-items-center gap-2.5">
                    <i class="bi bi-send-fill text-success fs-6"></i>
                    <span>Sent Dispatches</span>
                </div>
                <span class="text-muted" style="font-size: 0.8125rem;">{{ $counts['sent'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.emails.index', ['folder' => 'drafts']) }}" class="nav-link rounded-3 px-3 py-2.5 d-flex align-items-center justify-content-between {{ ($folder ?? '') === 'drafts' ? 'active fw-bold' : 'text-body hover-bg-body-tertiary' }}" style="font-size: 0.9375rem;">
                <div class="d-flex align-items-center gap-2.5">
                    <i class="bi bi-file-earmark-text-fill text-info fs-6"></i>
                    <span>Drafts</span>
                </div>
                <span class="text-muted" style="font-size: 0.8125rem;">{{ $counts['drafts'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.emails.index', ['folder' => 'trash']) }}" class="nav-link rounded-3 px-3 py-2.5 d-flex align-items-center justify-content-between {{ ($folder ?? '') === 'trash' ? 'active fw-bold' : 'text-body hover-bg-body-tertiary' }}" style="font-size: 0.9375rem;">
                <div class="d-flex align-items-center gap-2.5">
                    <i class="bi bi-trash3-fill text-danger fs-6"></i>
                    <span>Trash</span>
                </div>
                <span class="text-muted" style="font-size: 0.8125rem;">{{ $counts['trash'] ?? 0 }}</span>
            </a>
        </li>
    </ul>

    <!-- Quick Info -->
    <div class="p-3 bg-body-tertiary rounded-4 border text-muted" style="font-size: 0.8125rem; line-height: 1.45;">
        <div class="fw-bold text-body mb-1"><i class="bi bi-shield-check text-success me-1"></i> Maison Webmail</div>
        <span>Connected to public Salon inquiries and direct internal editorial dispatches.</span>
    </div>
</div>
