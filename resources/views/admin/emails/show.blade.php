@extends('admin.layouts.admin')

@section('title', $email->subject . ' | XIVIG Maison Gazette Mailbox')
@section('page_title', 'Read Dispatch')

@section('content')
<div class="row g-4">
    <!-- Left: Mailbox Folders Sidebar (3 cols) -->
    <div class="col-lg-3">
        @include('admin.emails.partials.sidebar')
    </div>

    <!-- Right: Message Detail View (9 cols) -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body mb-4">
            <!-- Header Action Bar -->
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-body-tertiary">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.emails.index', ['folder' => $email->folder]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Back to {{ ucfirst($email->folder) }}
                    </a>

                    <form action="{{ route('admin.emails.star', $email) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="{{ $email->is_starred ? 'Unstar' : 'Star' }}">
                            <i class="bi {{ $email->is_starred ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                        </button>
                    </form>

                    <form action="{{ route('admin.emails.read', $email) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Mark as unread">
                            <i class="bi bi-envelope"></i>
                        </button>
                    </form>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>

                    @if($email->folder !== 'trash')
                        <form action="{{ route('admin.emails.trash', $email) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" title="Move to Trash">
                                <i class="bi bi-trash3 me-1"></i> Trash
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.emails.restore', $email) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                            </button>
                        </form>
                        <form action="{{ route('admin.emails.destroy', $email) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this dispatch?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Message Detail Container -->
            <div class="p-4 p-md-5">
                <!-- Subject Headline & Folder Tag -->
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                    <h4 class="fw-black text-body mb-0" style="letter-spacing: -0.5px;">{{ $email->subject }}</h4>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 extra-small text-uppercase">
                        Folder: {{ $email->folder }}
                    </span>
                </div>

                <!-- Sender Meta Row -->
                <div class="d-flex align-items-center justify-content-between p-3 bg-body-tertiary rounded-4 border mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-black shadow-sm" style="width: 46px; height: 46px; font-size: 1.2rem;">
                            {{ strtoupper(substr($email->sender_name ?: $email->sender_email, 0, 1)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0 text-body">{{ $email->sender_name ?: 'Correspondent' }}</h6>
                                <span class="text-muted extra-small font-monospace">&lt;{{ $email->sender_email }}&gt;</span>
                            </div>
                            <span class="text-muted extra-small">
                                To: <strong class="text-body">{{ $email->recipient_name ?: $email->recipient_email }}</strong> &lt;{{ $email->recipient_email }}&gt;
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="fw-semibold small text-body d-block">{{ \App\Helpers\TimezoneHelper::format($email->created_at, 'M d, Y h:i A') }}</span>
                        <small class="text-muted extra-small">{{ $email->created_at->diffForHumans() }}</small>
                    </div>
                </div>

                <!-- Email Message Content -->
                <div class="p-4 bg-body rounded-4 border mb-4 text-body" style="line-height: 1.7; font-size: 0.95rem; min-height: 180px;">
                    {!! $email->body !!}
                </div>

                <!-- Attachments Section (if present) -->
                @if($email->attachment_path)
                    <div class="p-3 bg-body-tertiary rounded-4 border mb-4">
                        <div class="fw-bold small text-body mb-2"><i class="bi bi-paperclip text-primary me-1"></i> Attached Asset:</div>
                        <div class="d-inline-flex align-items-center gap-3 p-2 px-3 bg-body rounded-3 border">
                            <i class="bi bi-file-earmark-arrow-down fs-4 text-primary"></i>
                            <div>
                                <div class="fw-bold extra-small text-body">{{ $email->attachment_name ?: basename($email->attachment_path) }}</div>
                                <span class="text-muted extra-small">Click to download asset</span>
                            </div>
                            <a href="{{ asset($email->attachment_path) }}" target="_blank" download class="btn btn-sm btn-outline-primary rounded-pill px-3 ms-2 fw-bold">
                                Download
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Inline Quick Reply Form -->
                <div class="card border border-primary border-opacity-25 rounded-4 shadow-sm p-4 bg-body">
                    <h6 class="fw-black text-body mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-reply-fill text-primary fs-5"></i>
                        <span>Transmit Reply Dispatch</span>
                    </h6>

                    <form action="{{ route('admin.emails.send') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="recipient_email" value="{{ $email->sender_email }}">
                        <input type="hidden" name="recipient_name" value="{{ $email->sender_name }}">
                        <input type="hidden" name="subject" value="Re: {{ preg_replace('/^Re:\s*/i', '', $email->subject) }}">
                        <input type="hidden" name="reply_to_id" value="{{ $email->id }}">

                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted">Recipient:</label>
                            <span class="badge bg-body-tertiary text-body border px-3 py-2 rounded-3 extra-small ms-1">
                                {{ $email->sender_name ?: $email->sender_email }} &lt;{{ $email->sender_email }}&gt;
                            </span>
                        </div>

                        <div class="mb-3">
                            <textarea name="body" rows="4" class="form-control rounded-3" placeholder="Compose your official editorial response..." required></textarea>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label for="replyAttachment" class="btn btn-outline-secondary btn-sm rounded-pill px-3 cursor-pointer" style="cursor: pointer;">
                                    <i class="bi bi-paperclip me-1"></i> Add Attachment
                                </label>
                                <input type="file" name="attachment" id="replyAttachment" class="d-none" onchange="document.getElementById('replyAttachmentName').textContent = this.files[0]?.name || ''">
                                <span class="extra-small text-muted font-monospace" id="replyAttachmentName"></span>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('admin.emails.compose', ['reply_to' => $email->id]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    Full Editor Mode
                                </a>
                                <button type="submit" name="action" value="send" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-primary">
                                    <i class="bi bi-send-fill me-1"></i> Send Reply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
