@extends('admin.layouts.admin')

@section('title', 'Inquiry from ' . $contact->name . ' | XIVIG Maison Gazette')
@section('page_title', 'Inquiry Details & Response')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body mb-4">
            <!-- Header Action Bar -->
            <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between bg-body-tertiary">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Back to Inquiries
                    </a>

                    <form action="{{ route('admin.contacts.read', $contact) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Toggle Status">
                            <i class="bi {{ $contact->is_read ? 'bi-envelope' : 'bi-check2' }} me-1"></i>
                            {{ $contact->is_read ? 'Mark as Pending' : 'Mark as Reviewed' }}
                        </button>
                    </form>
                </div>

                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="d-inline" onsubmit="return confirm('Purge this inquiry record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" title="Delete Inquiry">
                        <i class="bi bi-trash3 me-1"></i> Purge Record
                    </button>
                </form>
            </div>

            <!-- Inquiry Body -->
            <div class="p-4 p-md-5">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <span class="badge bg-gold text-dark extra-small text-uppercase tracking-wider px-3 py-1 rounded-pill mb-2 fw-bold">
                            Salon Desk Inquiry #{{ $contact->id }}
                        </span>
                        <h4 class="fw-black text-body mb-0">{{ $contact->subject ?: 'General Inquiries & Atelier Correspondence' }}</h4>
                    </div>

                    @if(!$contact->is_read)
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1.5 extra-small fw-bold">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Pending Review
                        </span>
                    @else
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 extra-small fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Reviewed
                        </span>
                    @endif
                </div>

                <!-- Sender Meta Card -->
                <div class="d-flex align-items-center justify-content-between p-3 px-4 bg-body-tertiary rounded-4 border mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-black shadow-sm" style="width: 48px; height: 48px; font-size: 1.25rem;">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0 text-body">{{ $contact->name }}</h6>
                                <span class="badge bg-body border text-muted extra-small font-monospace">&lt;{{ $contact->email }}&gt;</span>
                            </div>
                            <span class="text-muted extra-small">
                                Transmitted from IP: <span class="font-monospace">{{ $contact->ip_address ?? '127.0.0.1' }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="fw-semibold small text-body d-block">{{ $contact->formattedTime() }}</span>
                        <small class="text-muted extra-small">{{ $contact->created_at->diffForHumans() }}</small>
                    </div>
                </div>

                <!-- Message Box -->
                <div class="p-4 bg-body rounded-4 border mb-5 text-body" style="line-height: 1.75; font-size: 0.95rem; min-height: 140px;">
                    {!! nl2br(e($contact->message)) !!}
                </div>

                <!-- Reply Section -->
                <div class="card border border-primary border-opacity-25 rounded-4 shadow-sm p-4 bg-body">
                    <h5 class="fw-black text-body mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-reply-all-fill text-primary"></i>
                        <span>Transmit Direct Editorial Reply</span>
                    </h5>
                    <p class="text-muted extra-small mb-3">Your response will be recorded into the Maison Dispatch Mailbox and transmitted to the patron's email.</p>

                    <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted">Response Subject:</label>
                            <input type="text" name="subject" value="Re: {{ $contact->subject ?: 'Inquiry with Maison XIVIG' }}" class="form-control rounded-3" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label extra-small fw-bold text-muted">Message Dispatch Content:</label>
                            <textarea name="reply_message" rows="5" class="form-control rounded-3" placeholder="Compose your response to {{ $contact->name }}..." required></textarea>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted extra-small font-monospace">To: {{ $contact->email }}</span>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary">
                                <i class="bi bi-send-fill me-1"></i> Send Reply Dispatch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
