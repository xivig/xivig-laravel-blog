@extends('admin.layouts.admin')

@section('title', 'Mailbox & Editorial Inquiries | XIVIG Maison Gazette')
@section('page_title', 'Editorial Dispatches & Mailbox')

@section('content')
<div class="row g-4">
    <!-- Left: Mailbox Folders Sidebar (3 cols) -->
    <div class="col-lg-3">
        @include('admin.emails.partials.sidebar')
    </div>

    <!-- Right: Emails List & Action Center (9 cols) -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body">
            <!-- Search & Bulk Toolbar -->
            <form action="{{ route('admin.emails.bulk') }}" method="POST" id="bulkForm">
                @csrf
                <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 bg-body-tertiary">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check ms-2 mb-0">
                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                        </div>

                        <div class="btn-group btn-group-sm">
                            <button type="submit" name="action" value="read" class="btn btn-outline-secondary" title="Mark as Read">
                                <i class="bi bi-envelope-open"></i>
                            </button>
                            <button type="submit" name="action" value="unread" class="btn btn-outline-secondary" title="Mark as Unread">
                                <i class="bi bi-envelope"></i>
                            </button>
                            <button type="submit" name="action" value="star" class="btn btn-outline-secondary" title="Star Messages">
                                <i class="bi bi-star"></i>
                            </button>
                            @if(($folder ?? '') === 'trash')
                                <button type="submit" name="action" value="delete" class="btn btn-outline-danger" title="Permanently Delete" onclick="return confirm('Permanently delete selected items?');">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <button type="submit" name="action" value="trash" class="btn btn-outline-danger" title="Move to Trash">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>

                        <a href="{{ route('admin.emails.index', ['folder' => $folder]) }}" class="btn btn-outline-secondary btn-sm" title="Refresh">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>

                    <!-- Search Input -->
                    <div class="d-flex align-items-center">
                        <div class="input-group input-group-sm" style="max-width: 280px;">
                            <input type="text" name="q" form="searchForm" class="form-control rounded-start-3" placeholder="Search dispatches..." value="{{ $search ?? '' }}">
                            <button type="submit" form="searchForm" class="btn btn-primary rounded-end-3">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <tbody class="border-top-0">
                            @forelse($emails as $item)
                                <tr class="{{ !$item->is_read && $item->folder === 'inbox' ? 'bg-primary bg-opacity-10 fw-bold' : '' }} transition-all">
                                    <td class="ps-3" style="width: 40px;">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input item-checkbox" type="checkbox" name="selected[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td style="width: 40px;" class="px-0">
                                        <button type="button" class="btn p-0 border-0 star-toggle-btn" onclick="toggleEmailStar(this, {{ $item->id }})">
                                            <i class="bi {{ $item->is_starred ? 'bi-star-fill text-warning' : 'bi-star text-muted opacity-50' }} fs-6"></i>
                                        </button>
                                    </td>
                                    <td style="width: 230px;" class="pe-2">
                                        <a href="{{ route('admin.emails.show', $item) }}" class="text-decoration-none text-body d-block text-truncate" style="font-size: 0.9375rem; font-weight: {{ !$item->is_read && $item->folder === 'inbox' ? '700' : '500' }};">
                                            @if($folder === 'sent')
                                                <span class="text-muted" style="font-size: 0.8125rem;">To:</span> {{ $item->recipient_name ?: $item->recipient_email }}
                                            @else
                                                {{ $item->sender_name ?: $item->sender_email }}
                                            @endif
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.emails.show', $item) }}" class="text-decoration-none text-body d-flex align-items-center gap-2">
                                            <span class="text-truncate" style="max-width: 480px; font-size: 0.9375rem;">
                                                <span class="{{ !$item->is_read && $item->folder === 'inbox' ? 'fw-bold text-body' : 'text-body' }}">{{ $item->subject }}</span>
                                                <span class="text-muted fw-normal ms-1" style="font-size: 0.85rem;">— {{ Str::limit(strip_tags($item->body), 75) }}</span>
                                            </span>
                                        </a>
                                    </td>
                                    <td style="width: 30px;" class="text-center px-1">
                                        @if($item->attachment_path)
                                            <i class="bi bi-paperclip text-muted fs-6" title="Has attachment"></i>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap" style="width: 140px;">
                                        <span class="text-muted" style="font-size: 0.8125rem;">
                                            {{ \App\Helpers\TimezoneHelper::format($item->created_at, 'M d, h:i A') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-2"></i>
                                        <h6 class="fw-bold text-muted">No messages found in {{ ucfirst($folder) }}</h6>
                                        <p class="text-muted extra-small mb-0">Incoming correspondence from the Maison Salon and editorial despatches will appear here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Separate Search Form to prevent nesting -->
            <form action="{{ route('admin.emails.index') }}" method="GET" id="searchForm" class="d-none">
                <input type="hidden" name="folder" value="{{ $folder }}">
            </form>

            @if($emails->hasPages())
                <div class="p-3 bg-body-tertiary border-top d-flex justify-content-end">
                    {{ $emails->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSelectAll(master) {
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.checked = master.checked;
    });
}

async function toggleEmailStar(btn, emailId) {
    const icon = btn.querySelector('i');
    try {
        const res = await fetch(`/admin/emails/${emailId}/star`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();
        if (data.is_starred) {
            icon.className = 'bi bi-star-fill text-warning fs-6';
        } else {
            icon.className = 'bi bi-star text-muted opacity-50 fs-6';
        }
    } catch(err) {
        console.error(err);
    }
}
</script>
@endpush
@endsection
