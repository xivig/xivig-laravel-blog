@extends('admin.layouts.admin')

@section('title', 'Task Manager (Architect.OS)')
@section('parentTitle', 'Apps')

@section('content')
<div class="task-manager-module">
    <!-- Header: Architecture Banner & Task Creator -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-surface border border-subtle">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-gold text-dark fw-bold text-uppercase letter-spacing-1 px-2 py-1" style="font-size: 0.65rem;">Execution Engine</span>
                    <h3 class="mb-0 fw-black text-body">Architect.OS</h3>
                </div>
                <p class="text-secondary small mb-0 fw-medium">Drag. Drop. Execute. Editorial dispatches and atelier workflows.</p>
            </div>

            <!-- Quick Task Creator Form -->
            <form id="createTaskForm" class="d-flex flex-wrap gap-2 align-items-center" action="{{ route('admin.task-manager.store') }}" method="POST">
                @csrf
                <input id="newTaskTitle" name="title" class="form-control bg-body border-subtle text-body rounded-pill px-3 shadow-none" placeholder="Task description..." style="min-width: 260px;" required />
                
                <select id="newTaskAvatar" name="avatar_badge" class="form-select bg-body border-subtle text-body rounded-pill px-3 shadow-none" style="width: 140px;">
                    <option value="AL" data-color="#ff4757">AL (Design)</option>
                    <option value="Dev" data-color="#2ed573">Dev (Code)</option>
                    <option value="QA" data-color="#ffa502">QA (Test)</option>
                    <option value="Ed" data-color="#d4af37">Ed (Editorial)</option>
                </select>
                <input type="hidden" name="badge_color" id="newTaskColor" value="#ff4757">
                <input type="hidden" name="status" value="sprint">

                <button type="submit" id="addTaskBtn" class="btn btn-gold rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Create</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Kanban / Sprint Columns -->
    <div class="row g-4">
        <!-- 1. Active Sprint (Full Width Top Highlight) -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-surface border border-subtle task-column-card">
                <div class="card-header bg-transparent p-4 border-bottom border-subtle d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dot-indicator bg-primary"></span>
                        <h5 class="mb-0 fw-bold text-body">Active Sprint</h5>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold extra-small">
                        In Progress ({{ $sprintTasks->count() }})
                    </span>
                </div>
                <div class="card-body p-4 kanban-dropzone" id="sprintTasks" data-status="sprint" style="min-height: 120px;">
                    @forelse($sprintTasks as $task)
                        <div class="task-card p-3 mb-3 rounded-3 bg-body border border-subtle shadow-xs d-flex align-items-center justify-content-between cursor-grab"
                             data-id="{{ $task->id }}" draggable="true">
                            <div class="d-flex align-items-center gap-3">
                                <span class="task-avatar-badge rounded-circle text-white fw-bold d-flex align-items-center justify-content-center"
                                      style="width: 32px; height: 32px; font-size: 0.75rem; background-color: {{ $task->badge_color }};">
                                    {{ $task->avatar_badge }}
                                </span>
                                <div>
                                    <div class="fw-semibold text-body small task-title">{{ $task->title }}</div>
                                    <span class="text-secondary extra-small">Created {{ $task->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-link text-secondary p-1 delete-task-btn" title="Delete" data-id="{{ $task->id }}">
                                    <i class="bi bi-trash3 text-danger opacity-75"></i>
                                </button>
                                <i class="bi bi-grip-vertical text-secondary opacity-50"></i>
                            </div>
                        </div>
                    @empty
                        <div class="empty-placeholder text-center py-4 text-secondary small">
                            <i class="bi bi-inbox fs-2 text-muted opacity-50 d-block mb-1"></i>
                            No active sprint dispatches. Create one above or drag from Queue.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 2. Queue (Upcoming) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-surface border border-subtle task-column-card h-100">
                <div class="card-header bg-transparent p-4 border-bottom border-subtle d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dot-indicator bg-warning"></span>
                        <h5 class="mb-0 fw-bold text-body">Queue</h5>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold extra-small">
                        Upcoming ({{ $queueTasks->count() }})
                    </span>
                </div>
                <div class="card-body p-4 kanban-dropzone" id="queueTasks" data-status="queue" style="min-height: 180px;">
                    @forelse($queueTasks as $task)
                        <div class="task-card p-3 mb-3 rounded-3 bg-body border border-subtle shadow-xs d-flex align-items-center justify-content-between cursor-grab"
                             data-id="{{ $task->id }}" draggable="true">
                            <div class="d-flex align-items-center gap-3">
                                <span class="task-avatar-badge rounded-circle text-white fw-bold d-flex align-items-center justify-content-center"
                                      style="width: 32px; height: 32px; font-size: 0.75rem; background-color: {{ $task->badge_color }};">
                                    {{ $task->avatar_badge }}
                                </span>
                                <div>
                                    <div class="fw-semibold text-body small task-title">{{ $task->title }}</div>
                                    <span class="text-secondary extra-small">Created {{ $task->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-link text-secondary p-1 delete-task-btn" title="Delete" data-id="{{ $task->id }}">
                                    <i class="bi bi-trash3 text-danger opacity-75"></i>
                                </button>
                                <i class="bi bi-grip-vertical text-secondary opacity-50"></i>
                            </div>
                        </div>
                    @empty
                        <div class="empty-placeholder text-center py-4 text-secondary small">
                            <i class="bi bi-hourglass text-muted opacity-50 fs-2 d-block mb-1"></i>
                            Queue is currently clear.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 3. Archived (Finished) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-surface border border-subtle task-column-card h-100">
                <div class="card-header bg-transparent p-4 border-bottom border-subtle d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="dot-indicator bg-success"></span>
                        <h5 class="mb-0 fw-bold text-body">Archived</h5>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold extra-small">
                        Finished ({{ $archivedTasks->count() }})
                    </span>
                </div>
                <div class="card-body p-4 kanban-dropzone" id="archivedTasks" data-status="archived" style="min-height: 180px;">
                    @forelse($archivedTasks as $task)
                        <div class="task-card p-3 mb-3 rounded-3 bg-body border border-subtle shadow-xs d-flex align-items-center justify-content-between cursor-grab opacity-75"
                             data-id="{{ $task->id }}" draggable="true">
                            <div class="d-flex align-items-center gap-3">
                                <span class="task-avatar-badge rounded-circle text-white fw-bold d-flex align-items-center justify-content-center"
                                      style="width: 32px; height: 32px; font-size: 0.75rem; background-color: {{ $task->badge_color }};">
                                    {{ $task->avatar_badge }}
                                </span>
                                <div>
                                    <div class="fw-semibold text-body small task-title text-decoration-line-through">{{ $task->title }}</div>
                                    <span class="text-secondary extra-small">Completed {{ $task->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-link text-secondary p-1 delete-task-btn" title="Delete" data-id="{{ $task->id }}">
                                    <i class="bi bi-trash3 text-danger opacity-75"></i>
                                </button>
                                <i class="bi bi-grip-vertical text-secondary opacity-50"></i>
                            </div>
                        </div>
                    @empty
                        <div class="empty-placeholder text-center py-4 text-secondary small">
                            <i class="bi bi-check2-circle text-muted opacity-50 fs-2 d-block mb-1"></i>
                            No completed dispatches archived.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-grab { cursor: grab; }
    .cursor-grab:active { cursor: grabbing; }
    .task-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    .kanban-dropzone.drag-over {
        background-color: rgba(212, 175, 55, 0.05);
        border: 2px dashed #d4af37 !important;
        border-radius: 12px;
    }
    .dot-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sync badge color when dropdown changes
    const avatarSelect = document.getElementById('newTaskAvatar');
    const colorInput = document.getElementById('newTaskColor');
    avatarSelect.addEventListener('change', function() {
        const selected = avatarSelect.options[avatarSelect.selectedIndex];
        colorInput.value = selected.dataset.color || '#ff4757';
    });

    // Native Drag and Drop Implementation
    let draggedItem = null;

    function initDragAndDrop() {
        document.querySelectorAll('.task-card').forEach(card => {
            card.addEventListener('dragstart', function(e) {
                draggedItem = this;
                this.classList.add('opacity-50');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.dataset.id);
            });

            card.addEventListener('dragend', function() {
                this.classList.remove('opacity-50');
                draggedItem = null;
                document.querySelectorAll('.kanban-dropzone').forEach(z => z.classList.remove('drag-over'));
            });
        });

        document.querySelectorAll('.kanban-dropzone').forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.classList.add('drag-over');
            });

            zone.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                if (!draggedItem) return;

                const newStatus = this.dataset.status;
                const taskId = draggedItem.dataset.id;

                // Remove placeholder if present
                const placeholder = this.querySelector('.empty-placeholder');
                if (placeholder) placeholder.style.display = 'none';

                this.appendChild(draggedItem);

                // Update status on backend
                fetch(`{{ url('admin/task-manager') }}/${taskId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                // Style toggle for finished items
                const titleEl = draggedItem.querySelector('.task-title');
                if (newStatus === 'archived') {
                    titleEl.classList.add('text-decoration-line-through');
                    draggedItem.classList.add('opacity-75');
                } else {
                    titleEl.classList.remove('text-decoration-line-through');
                    draggedItem.classList.remove('opacity-75');
                }
            });
        });
    }

    initDragAndDrop();

    // Delete task handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-task-btn');
        if (btn) {
            const taskId = btn.dataset.id;
            const card = btn.closest('.task-card');
            if (confirm('Delete this dispatch task?')) {
                fetch(`{{ url('admin/task-manager') }}/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    card.remove();
                });
            }
        }
    });
});
</script>
@endsection
