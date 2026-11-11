@extends('admin.layouts.admin')

@section('title', 'Editorial Calendar & Schedules')
@section('parentTitle', 'Apps')

@section('content')
<div class="row g-4">
    <!-- Left Sidebar: Draggable Events & Event Creator -->
    <div class="col-xl-3 col-lg-4">
        <!-- Draggable Events Card -->
        <div class="card border-0 shadow-sm mb-4 rounded-4 bg-surface border border-subtle">
            <div class="card-header bg-transparent py-3 border-bottom border-subtle d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-body"><i class="bi bi-grip-vertical text-gold me-2"></i>Draggable Dispatches</h6>
                <span class="badge bg-gold-subtle text-gold extra-small px-2 py-1 rounded-pill">Drag & Drop</span>
            </div>
            <div class="card-body" id="external-events">
                <p class="text-secondary extra-small mb-3">Drag dispatches directly onto the atelier schedule:</p>

                <div class="fc-event bg-success text-white border-0 p-2 px-3 mb-2 rounded-3 cursor-move shadow-xs d-flex align-items-center justify-content-between"
                    data-class="bg-success" data-color="success">
                    <span><i class="bi bi-cup-hot-fill me-2"></i>Atelier Lunch</span>
                    <i class="bi bi-arrows-move extra-small opacity-75"></i>
                </div>
                <div class="fc-event bg-warning text-dark border-0 p-2 px-3 mb-2 rounded-3 cursor-move shadow-xs d-flex align-items-center justify-content-between"
                    data-class="bg-warning" data-color="warning">
                    <span><i class="bi bi-camera-fill me-2"></i>Haute Couture Shoot</span>
                    <i class="bi bi-arrows-move extra-small opacity-75"></i>
                </div>
                <div class="fc-event bg-info text-white border-0 p-2 px-3 mb-2 rounded-3 cursor-move shadow-xs d-flex align-items-center justify-content-between"
                    data-class="bg-info" data-color="info">
                    <span><i class="bi bi-journal-text me-2"></i>Editorial Review</span>
                    <i class="bi bi-arrows-move extra-small opacity-75"></i>
                </div>
                <div class="fc-event bg-primary text-white border-0 p-2 px-3 mb-2 rounded-3 cursor-move shadow-xs d-flex align-items-center justify-content-between"
                    data-class="bg-primary" data-color="primary">
                    <span><i class="bi bi-palette-fill me-2"></i>Horology Preview</span>
                    <i class="bi bi-arrows-move extra-small opacity-75"></i>
                </div>
                <div class="fc-event bg-danger text-white border-0 p-2 px-3 mb-3 rounded-3 cursor-move shadow-xs d-flex align-items-center justify-content-between"
                    data-class="bg-danger" data-color="danger">
                    <span><i class="bi bi-send-fill me-2"></i>Gazette Release</span>
                    <i class="bi bi-arrows-move extra-small opacity-75"></i>
                </div>

                <div class="form-check form-switch small mt-3 pt-2 border-top border-subtle">
                    <input class="form-check-input" type="checkbox" id="drop-remove" checked>
                    <label class="form-check-label text-secondary small ms-2" for="drop-remove">
                        Remove from list after drop
                    </label>
                </div>
            </div>
        </div>

        <!-- Create Event Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-surface border border-subtle">
            <div class="card-header bg-transparent py-3 border-bottom border-subtle">
                <h6 class="mb-0 fw-bold text-body"><i class="bi bi-plus-circle text-gold me-2"></i>Create Custom Event</h6>
            </div>
            <div class="card-body">
                <label class="form-label text-secondary extra-small text-uppercase letter-spacing-1 fw-bold">Select Accent Color</label>
                <div class="d-flex gap-2 mb-3" id="event-color-picker">
                    <button type="button" class="btn btn-primary p-2 rounded-circle border-2 color-btn active" data-color="primary" style="width: 28px; height: 28px;"></button>
                    <button type="button" class="btn btn-warning p-2 rounded-circle border-0 color-btn" data-color="warning" style="width: 28px; height: 28px;"></button>
                    <button type="button" class="btn btn-success p-2 rounded-circle border-0 color-btn" data-color="success" style="width: 28px; height: 28px;"></button>
                    <button type="button" class="btn btn-danger p-2 rounded-circle border-0 color-btn" data-color="danger" style="width: 28px; height: 28px;"></button>
                    <button type="button" class="btn btn-info p-2 rounded-circle border-0 color-btn" data-color="info" style="width: 28px; height: 28px;"></button>
                    <button type="button" class="btn btn-secondary p-2 rounded-circle border-0 color-btn" data-color="secondary" style="width: 28px; height: 28px;"></button>
                </div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control bg-body border-subtle text-body rounded-start-3"
                        placeholder="Event title..." id="new-event-title">
                    <button class="btn btn-gold px-3 fw-bold rounded-end-3" id="add-new-event">
                        <i class="bi bi-plus-lg me-1"></i> Add
                    </button>
                </div>
                <span class="text-secondary extra-small">Creates an event in the draggable queue.</span>
            </div>
        </div>
    </div>

    <!-- Right: Calendar Canvas -->
    <div class="col-xl-9 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-surface border border-subtle">
            <div class="card-body p-4">
                <div id="calendar" style="min-height: 700px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Event Click/Details Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-surface">
            <div class="modal-header border-bottom border-subtle p-4">
                <h5 class="modal-title fw-bold text-body" id="eventDetailTitle">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-semibold">Event Title</label>
                    <input type="text" class="form-control bg-body border-subtle" id="modalEventTitle" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-semibold">Schedule Time</label>
                    <input type="text" class="form-control bg-body border-subtle" id="modalEventTime" readonly>
                </div>
            </div>
            <div class="modal-footer border-top border-subtle p-3">
                <button type="button" class="btn btn-danger rounded-pill px-4 btn-sm" id="btnDeleteEvent">
                    <i class="bi bi-trash3 me-1"></i> Delete Event
                </button>
                <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm ms-auto" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- FullCalendar Library Script --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
    .cursor-move { cursor: grab; }
    .cursor-move:active { cursor: grabbing; }
    .color-btn.active {
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.6);
        transform: scale(1.15);
    }
    /* FullCalendar Dark/Light Theme Integration */
    .fc {
        --fc-border-color: rgba(255, 255, 255, 0.08);
        --fc-page-bg-color: transparent;
        --fc-neutral-bg-color: rgba(255, 255, 255, 0.02);
        --fc-list-event-hover-bg-color: rgba(255, 255, 255, 0.05);
        --fc-today-bg-color: rgba(212, 175, 55, 0.08);
        font-family: inherit;
    }
    [data-bs-theme="light"] .fc {
        --fc-border-color: rgba(0, 0, 0, 0.08);
        --fc-today-bg-color: rgba(212, 175, 55, 0.12);
    }
    .fc .fc-toolbar-title {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .fc .fc-button-primary {
        background-color: var(--bs-body-bg);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: var(--bs-body-color);
        border-radius: 9999px !important;
        padding: 0.4rem 1rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: capitalize;
        box-shadow: none;
        transition: all 0.2s ease;
    }
    .fc .fc-button-primary:hover,
    .fc .fc-button-primary:focus,
    .fc .fc-button-primary.fc-button-active {
        background-color: #d4af37 !important;
        border-color: #d4af37 !important;
        color: #000 !important;
    }
    .fc-event {
        border-radius: 6px !important;
        padding: 2px 6px !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        cursor: pointer;
    }
    .fc-col-header-cell-cushion {
        padding: 10px 0 !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--bs-secondary);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedColor = 'primary';
    let activeEvent = null;

    // Color selector buttons
    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedColor = this.dataset.color;
        });
    });

    // Add new custom draggable event
    document.getElementById('add-new-event').addEventListener('click', function() {
        const titleInput = document.getElementById('new-event-title');
        const title = titleInput.value.trim();
        if (!title) return;

        const container = document.getElementById('external-events');
        const div = document.createElement('div');
        div.className = `fc-event bg-${selectedColor} text-white border-0 p-2 px-3 mb-2 rounded-3 cursor-move shadow-xs d-flex align-items-center justify-content-between`;
        div.dataset.class = `bg-${selectedColor}`;
        div.dataset.color = selectedColor;
        div.innerHTML = `<span><i class="bi bi-calendar-event me-2"></i>${title}</span><i class="bi bi-arrows-move extra-small opacity-75"></i>`;

        // Insert before the switch
        const switchEl = container.querySelector('.form-check');
        container.insertBefore(div, switchEl);

        titleInput.value = '';
    });

    // Initialize External Draggable Items
    const containerEl = document.getElementById('external-events');
    new FullCalendar.Draggable(containerEl, {
        itemSelector: '.fc-event',
        eventData: function(eventEl) {
            return {
                title: eventEl.innerText.trim(),
                className: eventEl.dataset.class,
                backgroundColor: eventEl.style.backgroundColor
            };
        }
    });

    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        initialView: 'dayGridMonth',
        editable: true,
        droppable: true,
        selectable: true,
        dayMaxEvents: true,
        events: '{{ route('admin.calendar.events') }}',
        drop: function(info) {
            if (document.getElementById('drop-remove').checked) {
                info.draggedEl.parentNode.removeChild(info.draggedEl);
            }
        },
        select: function(info) {
            const title = prompt('Enter new Atelier Chronicle Event title:');
            if (title) {
                const newEv = {
                    title: title,
                    start: info.startStr,
                    end: info.endStr,
                    allDay: info.allDay,
                    color: selectedColor
                };

                fetch('{{ route('admin.calendar.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(newEv)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        calendar.addEvent(data.event);
                    }
                });
            }
            calendar.unselect();
        },
        eventClick: function(info) {
            activeEvent = info.event;
            document.getElementById('modalEventTitle').value = activeEvent.title;
            document.getElementById('modalEventTime').value = activeEvent.start ? activeEvent.start.toLocaleString() : 'All day';
            const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
            modal.show();
        },
        eventDrop: function(info) {
            if (info.event.id) {
                fetch(`{{ url('admin/calendar/events') }}/${info.event.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        start: info.event.startStr,
                        end: info.event.endStr,
                        allDay: info.event.allDay
                    })
                });
            }
        }
    });

    calendar.render();

    // Delete event handler
    document.getElementById('btnDeleteEvent').addEventListener('click', function() {
        if (activeEvent && activeEvent.id) {
            fetch(`{{ url('admin/calendar/events') }}/${activeEvent.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                activeEvent.remove();
                bootstrap.Modal.getInstance(document.getElementById('eventDetailModal')).hide();
            });
        } else if (activeEvent) {
            activeEvent.remove();
            bootstrap.Modal.getInstance(document.getElementById('eventDetailModal')).hide();
        }
    });
});
</script>
@endsection
