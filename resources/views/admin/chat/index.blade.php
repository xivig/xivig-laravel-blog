@extends('admin.layouts.admin')

@section('title', 'Editorial Salon & Live Staff Chat | XIVIG Maison Gazette')
@section('page_title', 'Editorial Salon & Live Chat')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body" style="min-height: 700px;">
    <div class="row g-0 flex-nowrap" style="height: 700px;">
        <!-- Left Panel: Channels & Direct Staff (30%) -->
        <div class="col-4 col-xl-3 border-end d-flex flex-column h-100 bg-body-tertiary" style="min-width: 280px;">
            <!-- Search & Status Header -->
            <div class="p-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-uppercase text-body" style="font-size: 0.85rem; letter-spacing: 0.5px;">Maison Channels</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1" style="font-size: 0.78rem;">
                        <i class="bi bi-circle-fill me-1 text-success" style="font-size: 0.5rem;"></i> Live Sync
                    </span>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0 py-2"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="chatSearchInput" class="form-control bg-body border-start-0 rounded-end-3 py-2" placeholder="Filter channels or staff..." onkeyup="filterChatList(this.value)" style="font-size: 0.875rem;">
                </div>
            </div>

            <!-- Scrollable Channels & Users List -->
            <div class="flex-grow-1 overflow-y-auto custom-scroll p-3">
                <!-- Atelier Salon Channels -->
                <div class="fw-bold text-muted text-uppercase px-2 mb-2" style="font-size: 0.78rem; letter-spacing: 0.08em;">
                    EDITORIAL DESKS
                </div>
                <div class="d-flex flex-column gap-1 mb-4" id="channelsList">
                    @foreach($channels as $cKey => $channel)
                        @php $isActive = !$activeRecipient && $currentChannel === $cKey; @endphp
                        <a href="{{ route('admin.chat.index', ['channel' => $cKey]) }}" class="chat-sidebar-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none transition-all {{ $isActive ? 'bg-primary text-white shadow-sm' : 'text-body hover-bg-body' }}">
                            <div class="icon-shape {{ $isActive ? 'bg-white bg-opacity-20 text-white' : 'bg-primary bg-opacity-10 text-primary' }} rounded-circle flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi {{ $channel['icon'] }}" style="font-size: 1.05rem;"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold text-truncate" style="font-size: 0.9375rem;">{{ $channel['name'] }}</div>
                                <div class="opacity-75 text-truncate" style="font-size: 0.8125rem; line-height: 1.35;">{{ $channel['description'] }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Direct Messages with Staff -->
                <div class="fw-bold text-muted text-uppercase px-2 mb-2" style="font-size: 0.78rem; letter-spacing: 0.08em;">
                    STAFF DIRECT MESSAGES
                </div>
                <div class="d-flex flex-column gap-1" id="staffUsersList">
                    @forelse($staffUsers as $staff)
                        @php $isStaffActive = $activeRecipient && $activeRecipient->id === $staff->id; @endphp
                        <a href="{{ route('admin.chat.index', ['recipient_id' => $staff->id]) }}" class="chat-sidebar-item d-flex align-items-center gap-3 px-3 py-2.5 rounded-3 text-decoration-none transition-all {{ $isStaffActive ? 'bg-primary text-white shadow-sm' : 'text-body hover-bg-body' }}">
                            <div class="position-relative flex-shrink-0">
                                @if($staff->avatar)
                                    <img src="{{ $staff->avatar }}" alt="{{ $staff->name }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 38px; height: 38px;">
                                @else
                                    <div class="rounded-circle {{ $isStaffActive ? 'bg-white text-primary' : 'bg-secondary bg-opacity-20 text-body' }} d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-body" style="width: 10px; height: 10px;"></span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-0.5">
                                    <span class="fw-bold text-truncate" style="font-size: 0.9375rem;">{{ $staff->name }}</span>
                                    @if($staff->unread_count > 0)
                                        <span class="badge bg-danger rounded-pill ms-1 px-2 py-0.5" style="font-size: 0.75rem;">{{ $staff->unread_count }}</span>
                                    @endif
                                </div>
                                <div class="opacity-75 text-truncate" style="font-size: 0.8125rem;">{{ $staff->designation ?? $staff->role }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="px-3 py-4 text-muted text-center" style="font-size: 0.875rem;">No other staff registered.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Panel: Chat Stream & Message Input (70%) -->
        <div class="col-8 col-xl-9 d-flex flex-column h-100 bg-body">
            <!-- Conversation Header -->
            <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between bg-body">
                <div class="d-flex align-items-center gap-3">
                    @if($activeRecipient)
                        <div class="position-relative">
                            @if($activeRecipient->avatar)
                                <img src="{{ $activeRecipient->avatar }}" alt="{{ $activeRecipient->name }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 44px; height: 44px;">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-black shadow-sm" style="width: 44px; height: 44px; font-size: 1.15rem;">
                                    {{ strtoupper(substr($activeRecipient->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-body" style="width: 11px; height: 11px;"></span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-body" style="font-size: 1.15rem;">{{ $activeRecipient->name }}</h5>
                            <span class="text-muted" style="font-size: 0.85rem;">{{ $activeRecipient->designation ?? 'Staff Contributor' }} &bull; Direct Confidential Dispatch</span>
                        </div>
                    @else
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle p-2" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi {{ $channels[$currentChannel]['icon'] ?? 'bi-chat-dots-fill' }} fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-body" style="font-size: 1.15rem;">{{ $channels[$currentChannel]['name'] ?? 'Editorial Salon' }}</h5>
                            <span class="text-muted" style="font-size: 0.85rem;">{{ $channels[$currentChannel]['description'] ?? 'Atelier group discourse' }}</span>
                        </div>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" id="audioToggleBtn" title="Toggle Sound Chime" onclick="toggleAudio()">
                        <i class="bi bi-volume-up fs-6" id="audioIcon"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Refresh Chat" onclick="fetchMessages()">
                        <i class="bi bi-arrow-clockwise fs-6"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Stream Area -->
            <div class="flex-grow-1 overflow-y-auto custom-scroll p-4 d-flex flex-column gap-3" id="chatMessageStream" style="scroll-behavior: smooth;">
                @php $lastDate = null; @endphp
                @forelse($initialMessages as $msg)
                    @php
                        $msgDate = \App\Helpers\TimezoneHelper::format($msg->created_at, 'M d, Y');
                        $isMe = $msg->sender_id === Auth::id();
                    @endphp

                    @if($msgDate !== $lastDate)
                        <div class="text-center my-2">
                            <span class="badge bg-body-tertiary text-body border px-3 py-1.5 rounded-pill shadow-xs" style="font-size: 0.8125rem; font-weight: 600;">
                                {{ $msgDate }}
                            </span>
                        </div>
                        @php $lastDate = $msgDate; @endphp
                    @endif

                    <div class="d-flex gap-2.5 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} message-item" data-id="{{ $msg->id }}">
                        @if(!$isMe)
                            <div class="avatar-placeholder rounded-circle bg-secondary bg-opacity-25 text-body d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                {{ strtoupper(substr($msg->sender->name ?? 'M', 0, 1)) }}
                            </div>
                        @endif

                        <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}" style="max-width: 72%;">
                            @if(!$isMe)
                                <span class="fw-bold text-muted mb-1" style="font-size: 0.85rem;">{{ $msg->sender->name ?? 'Staff' }}</span>
                            @endif

                            <div class="p-3 px-4 rounded-4 shadow-sm {{ $isMe ? 'bg-primary text-white' : 'bg-body-tertiary text-body border' }}" style="font-size: 0.9375rem; line-height: 1.6; word-break: break-word;">
                                {!! nl2br(e($msg->message)) !!}

                                @if($msg->attachment)
                                    <div class="mt-2 pt-2 border-top {{ $isMe ? 'border-white border-opacity-25' : 'border-secondary border-opacity-25' }}">
                                        @php $ext = pathinfo($msg->attachment, PATHINFO_EXTENSION); @endphp
                                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                            <a href="{{ asset($msg->attachment) }}" target="_blank">
                                                <img src="{{ asset($msg->attachment) }}" alt="Attachment" class="img-fluid rounded-3 mb-1" style="max-height: 200px; object-fit: cover;">
                                            </a>
                                        @else
                                            <a href="{{ asset($msg->attachment) }}" target="_blank" class="d-inline-flex align-items-center gap-1.5 {{ $isMe ? 'text-white' : 'text-primary' }} text-decoration-none fw-bold" style="font-size: 0.85rem;">
                                                <i class="bi bi-file-earmark-arrow-down-fill"></i> Download Attached Asset
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <span class="text-muted mt-1" style="font-size: 0.78rem;">
                                {{ \App\Helpers\TimezoneHelper::format($msg->created_at, 'h:i A') }}
                            </span>
                        </div>

                        @if($isMe)
                            <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="my-auto text-center py-5" id="chatEmptyState">
                        <div class="icon-shape bg-body-tertiary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                            <i class="bi bi-chat-quote fs-1 text-primary opacity-75"></i>
                        </div>
                        <h5 class="fw-bold text-body">Atelier Salon Ready</h5>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Share live notes, editorial ideas, or article feedback with the Maison team.</p>
                    </div>
                @endforelse
            </div>

            <!-- Chat Input Toolbar & Form -->
            <div class="p-3 px-4 border-top bg-body">
                <!-- Emoji Quick Bar -->
                <div class="d-flex align-items-center gap-1.5 mb-2">
                    <span class="text-muted fw-bold me-1" style="font-size: 0.8125rem;">Quick:</span>
                    @foreach(['✨', '✍️', '💎', '⏳', '🖤', '📰', '🥂', '🔥'] as $emoji)
                        <button type="button" class="btn btn-sm btn-outline-secondary p-0 px-2 rounded-pill" onclick="insertEmoji('{{ $emoji }}')" style="font-size: 0.9rem;">{{ $emoji }}</button>
                    @endforeach
                    <div class="ms-auto" id="attachmentBadge" style="display: none;">
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1" style="font-size: 0.8125rem;">
                            <i class="bi bi-paperclip me-1"></i> <span id="attachmentName">file.png</span>
                            <i class="bi bi-x ms-1 cursor-pointer" onclick="clearAttachment()" style="cursor: pointer;"></i>
                        </span>
                    </div>
                </div>

                <form id="chatForm" onsubmit="sendChatMessage(event)" class="d-flex align-items-end gap-2.5">
                    @csrf
                    <input type="hidden" name="channel" value="{{ $currentChannel }}">
                    <input type="hidden" name="recipient_id" value="{{ $activeRecipient?->id }}">
                    <input type="file" id="chatAttachmentInput" class="d-none" onchange="onAttachmentSelected(this)">

                    <button type="button" class="btn btn-outline-secondary rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;" onclick="document.getElementById('chatAttachmentInput').click()" title="Attach image or file">
                        <i class="bi bi-paperclip fs-5"></i>
                    </button>

                    <div class="flex-grow-1 position-relative">
                        <textarea id="chatInputMessage" rows="1" class="form-control rounded-4 py-2.5 px-3 custom-scroll" placeholder="Type a message... (Press Enter to send, Shift+Enter for new line)" style="font-size: 0.9375rem; resize: none; max-height: 120px;" onkeydown="handleChatKeyDown(event)"></textarea>
                    </div>

                    <button type="submit" id="chatSendBtn" class="btn btn-primary rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center shadow-primary" style="width: 44px; height: 44px;" title="Send Dispatch">
                        <i class="bi bi-send-fill fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let lastMessageId = {{ $initialMessages->last()?->id ?? 0 }};
let soundEnabled = true;
let currentChannel = '{{ $currentChannel }}';
let currentRecipientId = '{{ $activeRecipient?->id ?? '' }}';
let audioCtx = null;

// Synthetic Web Audio API Chime (Zero external files needed)
function playChime() {
    if (!soundEnabled) return;
    try {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
        osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15); // A5
        gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.45);
    } catch(e) {}
}

function toggleAudio() {
    soundEnabled = !soundEnabled;
    const icon = document.getElementById('audioIcon');
    icon.className = soundEnabled ? 'bi bi-volume-up fs-6' : 'bi bi-volume-mute fs-6 text-danger';
}

function scrollToBottom() {
    const stream = document.getElementById('chatMessageStream');
    stream.scrollTop = stream.scrollHeight;
}

window.addEventListener('DOMContentLoaded', () => {
    scrollToBottom();
    setInterval(fetchMessages, 3500);
});

function insertEmoji(emoji) {
    const input = document.getElementById('chatInputMessage');
    input.value += emoji;
    input.focus();
}

function handleChatKeyDown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('chatForm').dispatchEvent(new Event('submit', { cancelable: true }));
    }
}

function onAttachmentSelected(input) {
    if (input.files && input.files[0]) {
        document.getElementById('attachmentName').textContent = input.files[0].name;
        document.getElementById('attachmentBadge').style.display = 'inline-block';
    }
}

function clearAttachment() {
    document.getElementById('chatAttachmentInput').value = '';
    document.getElementById('attachmentBadge').style.display = 'none';
}

function filterChatList(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.chat-sidebar-item').forEach(el => {
        const text = el.textContent.toLowerCase();
        el.style.display = text.includes(q) ? 'flex' : 'none';
    });
}

async function fetchMessages() {
    try {
        const url = new URL('{{ route("admin.chat.fetch") }}', window.location.origin);
        if (currentRecipientId) {
            url.searchParams.set('recipient_id', currentRecipientId);
        } else {
            url.searchParams.set('channel', currentChannel);
        }
        url.searchParams.set('after_id', lastMessageId);

        const res = await fetch(url.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.messages && data.messages.length > 0) {
            let hasIncoming = false;
            const stream = document.getElementById('chatMessageStream');
            const emptyState = document.getElementById('chatEmptyState');
            if (emptyState) emptyState.remove();

            data.messages.forEach(msg => {
                if (msg.id > lastMessageId) {
                    lastMessageId = msg.id;
                    appendMessageToStream(msg);
                    if (!msg.is_me) hasIncoming = true;
                }
            });

            if (hasIncoming) playChime();
            scrollToBottom();
        }
    } catch(err) {
        // Suppress network polling errors
    }
}

function appendMessageToStream(msg) {
    const stream = document.getElementById('chatMessageStream');
    const row = document.createElement('div');
    row.className = `d-flex gap-2.5 ${msg.is_me ? 'justify-content-end' : 'justify-content-start'} message-item`;
    row.dataset.id = msg.id;

    let attachmentHtml = '';
    if (msg.attachment_url) {
        const isImg = msg.attachment_url.match(/\.(jpeg|jpg|gif|png|webp)($|\?)/i);
        if (isImg) {
            attachmentHtml = `<div class="mt-2 pt-2 border-top ${msg.is_me ? 'border-white border-opacity-25' : 'border-secondary border-opacity-25'}">
                <a href="${msg.attachment_url}" target="_blank">
                    <img src="${msg.attachment_url}" class="img-fluid rounded-3 mb-1" style="max-height: 200px; object-fit: cover;">
                </a>
            </div>`;
        } else {
            attachmentHtml = `<div class="mt-2 pt-2 border-top ${msg.is_me ? 'border-white border-opacity-25' : 'border-secondary border-opacity-25'}">
                <a href="${msg.attachment_url}" target="_blank" class="d-inline-flex align-items-center gap-1.5 ${msg.is_me ? 'text-white' : 'text-primary'} text-decoration-none fw-bold" style="font-size: 0.85rem;">
                    <i class="bi bi-file-earmark-arrow-down-fill"></i> Download Attached Asset
                </a>
            </div>`;
        }
    }

    const initial = (msg.sender_name || 'M').charAt(0).toUpperCase();

    if (msg.is_me) {
        row.innerHTML = `
            <div class="d-flex flex-column align-items-end" style="max-width: 72%;">
                <div class="p-3 px-4 rounded-4 shadow-sm bg-primary text-white" style="font-size: 0.9375rem; line-height: 1.6; word-break: break-word;">
                    ${msg.message.replace(/\n/g, '<br>')}
                    ${attachmentHtml}
                </div>
                <span class="text-muted mt-1" style="font-size: 0.78rem;">${msg.formatted_time}</span>
            </div>
            <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.9rem;">
                ${initial}
            </div>
        `;
    } else {
        row.innerHTML = `
            <div class="avatar-placeholder rounded-circle bg-secondary bg-opacity-25 text-body d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.9rem;">
                ${initial}
            </div>
            <div class="d-flex flex-column align-items-start" style="max-width: 72%;">
                <span class="fw-bold text-muted mb-1" style="font-size: 0.85rem;">${msg.sender_name}</span>
                <div class="p-3 px-4 rounded-4 shadow-sm bg-body-tertiary text-body border" style="font-size: 0.9375rem; line-height: 1.6; word-break: break-word;">
                    ${msg.message.replace(/\n/g, '<br>')}
                    ${attachmentHtml}
                </div>
                <span class="text-muted mt-1" style="font-size: 0.78rem;">${msg.formatted_time}</span>
            </div>
        `;
    }

    stream.appendChild(row);
}

async function sendChatMessage(e) {
    e.preventDefault();
    const input = document.getElementById('chatInputMessage');
    const fileInput = document.getElementById('chatAttachmentInput');
    const message = input.value.trim();

    if (!message && (!fileInput.files || fileInput.files.length === 0)) {
        return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    if (message) formData.append('message', message);
    if (currentRecipientId) {
        formData.append('recipient_id', currentRecipientId);
    } else {
        formData.append('channel', currentChannel);
    }
    if (fileInput.files && fileInput.files[0]) {
        formData.append('attachment', fileInput.files[0]);
    }

    input.value = '';
    clearAttachment();

    try {
        const res = await fetch('{{ route("admin.chat.send") }}', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success && data.message) {
            lastMessageId = data.message.id;
            const emptyState = document.getElementById('chatEmptyState');
            if (emptyState) emptyState.remove();
            appendMessageToStream(data.message);
            scrollToBottom();
        }
    } catch(err) {
        console.error(err);
    }
}
</script>
@endpush
@endsection
