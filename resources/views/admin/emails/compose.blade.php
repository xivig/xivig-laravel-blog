@extends('admin.layouts.admin')

@section('title', 'Compose Dispatch | XIVIG Maison Gazette')
@section('page_title', 'Compose New Dispatch')

@section('content')
<div class="row g-4">
    <!-- Left: Mailbox Folders Sidebar (3 cols) -->
    <div class="col-lg-3">
        @include('admin.emails.partials.sidebar')
    </div>

    <!-- Right: Compose Form (9 cols) -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body">
            <div class="p-4 border-bottom bg-body-tertiary d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-black text-body mb-0">Editorial Dispatch Composer</h5>
                    <p class="text-muted extra-small mb-0">Transmit external correspondence or internal atelier memoranda.</p>
                </div>
                <a href="{{ route('admin.emails.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-x-lg me-1"></i> Discard
                </a>
            </div>

            <form action="{{ route('admin.emails.send') }}" method="POST" enctype="multipart/form-data" class="p-4 p-md-5">
                @csrf
                @if(!empty($prefill['reply_to_id']))
                    <input type="hidden" name="reply_to_id" value="{{ $prefill['reply_to_id'] }}">
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold text-muted">Recipient Email <span class="text-danger">*</span></label>
                        <input type="email" name="recipient_email" id="recipientEmailInput" value="{{ old('recipient_email', $prefill['to']) }}" required class="form-control rounded-3" placeholder="patron@domain.com">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-muted">Quick Staff Recipient</label>
                        <select class="form-select rounded-3" onchange="if(this.value) { document.getElementById('recipientEmailInput').value = this.value; }">
                            <option value="">Choose Staff Member...</option>
                            @foreach($staffUsers as $staff)
                                <option value="{{ $staff->email }}">{{ $staff->name }} ({{ $staff->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Recipient Name (Optional)</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name', $prefill['name']) }}" class="form-control rounded-3" placeholder="e.g. Madame de Valois">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject', $prefill['subject']) }}" required class="form-control rounded-3" placeholder="Chronicle Review &amp; Dispatch">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Message Content <span class="text-danger">*</span></label>
                    <textarea name="body" id="emailBodyEditor" rows="10" class="form-control rounded-3" placeholder="Compose your correspondence..." required>{{ old('body') }}</textarea>
                </div>

                <div class="p-3 bg-body-tertiary rounded-4 border mb-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <label for="composeAttachment" class="btn btn-outline-secondary btn-sm rounded-pill px-3 cursor-pointer" style="cursor: pointer;">
                            <i class="bi bi-paperclip me-1"></i> Attach Asset
                        </label>
                        <input type="file" name="attachment" id="composeAttachment" class="d-none" onchange="document.getElementById('composeAttachmentInfo').textContent = this.files[0]?.name || ''">
                        <span class="font-monospace extra-small text-muted" id="composeAttachmentInfo">Maximum size: 10MB</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-save me-1"></i> Save Draft
                        </button>
                        <button type="submit" name="action" value="send" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary">
                            <i class="bi bi-send-fill me-1"></i> Transmit Dispatch
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
