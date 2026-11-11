@extends('admin.layouts.admin')

@section('title', 'File Manager')
@section('parentTitle', 'Apps')

@section('content')
<div class="row g-4">
    <!-- Main Explorer Wrapper -->
    <div class="col-12">
        <div class="file-manager__wrapper rounded-4 overflow-hidden shadow-sm d-flex border border-subtle bg-surface">
            
            <!-- Sidebar: Navigation & Storage -->
            <aside class="file-sidebar d-none d-xl-flex flex-column border-end border-subtle py-4" style="width: 260px; min-width: 260px;">
                <div class="px-4 mb-4">
                    <h5 class="fw-black mb-1 text-body">Maison Cloud</h5>
                    <p class="text-secondary extra-small text-uppercase tracking-widest mb-0 letter-spacing-1">Elite Cloud Vault</p>
                </div>

                <nav class="nav flex-column gap-1 px-3">
                    <a class="nav-link py-2 px-3 rounded-pill fw-semibold small {{ !$status ? 'bg-gold text-dark' : 'text-body hover-gold' }}" href="{{ route('admin.file-manager.index') }}">
                        <i class="bi bi-grid-fill me-2"></i> All Assets
                    </a>
                    <a class="nav-link py-2 px-3 rounded-pill fw-semibold small {{ $status === 'trash' ? 'bg-danger text-white' : 'text-body hover-gold' }}" href="{{ route('admin.file-manager.index', ['status' => 'trash']) }}">
                        <i class="bi bi-trash3-fill me-2 {{ $status === 'trash' ? 'text-white' : 'text-danger' }}"></i> Bin (Trash)
                    </a>
                </nav>

                <div class="mt-4 px-4 pt-3 border-top border-subtle">
                    <label class="text-secondary extra-small text-uppercase letter-spacing-1 fw-bold mb-3 d-block">Categories</label>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.file-manager.index', ['search' => 'image']) }}" class="text-decoration-none small fw-semibold text-body d-flex align-items-center">
                            <span class="rounded-circle bg-info me-2" style="width: 8px; height: 8px;"></span> Imagery &amp; Covers
                        </a>
                        <a href="{{ route('admin.file-manager.index', ['search' => 'pdf']) }}" class="text-decoration-none small fw-semibold text-body d-flex align-items-center">
                            <span class="rounded-circle bg-danger me-2" style="width: 8px; height: 8px;"></span> Editorial PDFs
                        </a>
                        <a href="{{ route('admin.file-manager.index', ['search' => 'doc']) }}" class="text-decoration-none small fw-semibold text-body d-flex align-items-center">
                            <span class="rounded-circle bg-primary me-2" style="width: 8px; height: 8px;"></span> Manuscripts
                        </a>
                    </div>
                </div>

                <div class="mt-auto p-3 mx-3 mb-2 rounded-4 bg-body border border-subtle">
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="fw-bold extra-small text-secondary">ACTIVE ARCHIVE</span>
                        <span class="fw-bold text-gold extra-small">{{ $totalFilesCount }} Files</span>
                    </div>
                    <div class="progress mb-2 rounded-pill" style="height: 6px;">
                        <div class="progress-bar bg-gold" role="progressbar" style="width: {{ min(100, max(15, $totalFilesCount * 5)) }}%"></div>
                    </div>
                    <span class="text-secondary extra-small">{{ $totalFoldersCount }} Curated Folders</span>
                </div>
            </aside>

            <!-- Explorer: Toolbar & File Grid -->
            <main class="file-explorer flex-grow-1 p-0 d-flex flex-column" style="min-width: 0;">
                
                <!-- Advanced Toolbar -->
                <header class="file-toolbar p-4 border-bottom border-subtle">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <form action="{{ route('admin.file-manager.index') }}" method="GET" class="input-group bg-body rounded-pill px-3 py-1 border border-subtle">
                                <span class="input-group-text bg-transparent border-0 p-0 me-2"><i class="bi bi-search text-secondary"></i></span>
                                <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none small text-body" placeholder="Search archive files..." value="{{ request('search') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="folder_id" value="{{ request('folder_id') }}">
                            </form>
                        </div>
                        <div class="col-md-7 d-flex justify-content-md-end align-items-center gap-2">
                            @if($status !== 'trash')
                                <button class="btn btn-outline-editorial btn-sm px-3 py-2 rounded-pill shadow-xs fw-bold" data-bs-toggle="modal" data-bs-target="#newFolderModal">
                                    <i class="bi bi-folder-plus me-1 text-gold"></i> New Folder
                                </button>
                                <form action="{{ route('admin.file-manager.upload') }}" method="POST" enctype="multipart/form-data" class="d-inline-flex" id="upload-form">
                                    @csrf
                                    <input type="hidden" name="folder_id" value="{{ request('folder_id') }}">
                                    <input type="file" name="file" id="file-input" class="d-none" onchange="document.getElementById('upload-form').submit()">
                                    <button type="button" class="btn btn-gold btn-sm px-4 py-2 rounded-pill shadow-sm fw-bold border-0" onclick="document.getElementById('file-input').click()">
                                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload File
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.file-manager.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3">
                                    &larr; Exit Trash
                                </a>
                            @endif
                        </div>
                    </div>
                </header>

                <!-- Scrollable Explorer Content -->
                <div class="flex-grow-1 overflow-auto p-4 custom-scroll" style="min-height: 520px;">
                    <!-- Breadcrumbs -->
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.file-manager.index') }}" class="text-secondary text-decoration-none">Drive Root</a></li>
                            @if($currentFolder)
                                <li class="breadcrumb-item active fw-bold text-gold" aria-current="page">{{ $currentFolder->name }}</li>
                            @endif
                        </ol>
                    </nav>

                    <!-- Folders Section -->
                    @if($folders->count() > 0)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-secondary extra-small text-uppercase fw-bold letter-spacing-1 mb-0">Folders ({{ $folders->count() }})</h6>
                    </div>
                    <div class="row g-3 mb-5">
                        @foreach($folders as $folder)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="file-item card h-100 p-0 text-center shadow-xs border border-subtle transition-all bg-body position-relative">
                                <a href="{{ route('admin.file-manager.index', ['folder_id' => $folder->id, 'status' => $status]) }}" class="text-decoration-none p-3 d-block">
                                    <div class="file-item__icon mb-2">
                                        <i class="bi bi-folder-fill fs-1 text-{{ $folder->color }}"></i>
                                    </div>
                                    <h6 class="file-item__name text-body fw-bold small text-truncate mb-1">{{ $folder->name }}</h6>
                                    <p class="file-item__meta mb-0 extra-small text-secondary">{{ $folder->files->count() }} Items</p>
                                </a>
                                
                                <div class="position-absolute top-0 end-0 m-2">
                                    <button class="btn btn-link text-secondary p-0 border-0 shadow-none" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}'>
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-subtle bg-surface">
                                        @if($folder->trashed())
                                            <li>
                                                <form action="{{ route('admin.file-manager.folder.restore', $folder->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2 small text-success"><i class="bi bi-arrow-counterclockwise me-2"></i> Restore</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.file-manager.folder.forceDelete', $folder->id) }}" method="POST" onsubmit="return confirm('Permanently destroy this folder and all its items?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 small text-danger"><i class="bi bi-x-circle me-2"></i> Destroy Permanently</button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <button type="button" class="dropdown-item py-2 small" onclick="openRenameModal({{ $folder->id }}, '{{ addslashes($folder->name) }}')">
                                                    <i class="bi bi-pencil-square me-2 text-primary"></i> Rename
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider border-subtle"></li>
                                            <li>
                                                <form action="{{ route('admin.file-manager.folder.delete', $folder->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 small text-danger"><i class="bi bi-trash3 me-2"></i> Move to Bin</button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Files Section -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-secondary extra-small text-uppercase fw-bold letter-spacing-1 mb-0">Files ({{ $files->count() }})</h6>
                    </div>
                    <div class="row g-3">
                        @forelse($files as $file)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="file-item card h-100 p-0 text-center shadow-xs border border-subtle transition-all bg-body position-relative">
                                <div class="p-3 d-block">
                                    <div class="file-item__icon mb-2">
                                        <i class="bi {{ $file->icon }} fs-1 text-{{ $file->color }}"></i>
                                    </div>
                                    <h6 class="file-item__name text-body fw-bold small text-truncate px-2 mb-1" title="{{ $file->name }}">{{ $file->name }}</h6>
                                    <p class="file-item__meta mb-0 extra-small text-secondary">{{ $file->size }}</p>
                                </div>

                                <div class="position-absolute top-0 end-0 m-2">
                                    <button class="btn btn-link text-secondary p-0 border-0 shadow-none" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}'>
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-subtle bg-surface">
                                        @if($file->trashed())
                                            <li>
                                                <form action="{{ route('admin.file-manager.file.restore', $file->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2 small text-success"><i class="bi bi-arrow-counterclockwise me-2"></i> Restore</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.file-manager.file.forceDelete', $file->id) }}" method="POST" onsubmit="return confirm('Permanently destroy this file?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 small text-danger"><i class="bi bi-x-circle me-2"></i> Destroy Permanently</button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ asset($file->path) }}" target="_blank" download class="dropdown-item py-2 small text-body">
                                                    <i class="bi bi-download me-2 text-success"></i> Download
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider border-subtle"></li>
                                            <li>
                                                <form action="{{ route('admin.file-manager.file.delete', $file->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 small text-danger"><i class="bi bi-trash3 me-2"></i> Move to Bin</button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 py-5 text-center text-secondary">
                            <i class="bi bi-folder-x fs-1 text-muted opacity-50 d-block mb-2"></i>
                            <h6 class="fw-bold mb-1">No files located in this directory.</h6>
                            <p class="extra-small text-secondary mb-0">Use the Upload button above to add documents, imagery, and assets.</p>
                        </div>
                        @endforelse
                    </div>

                </div>
            </main>
        </div>
    </div>
</div>

<!-- New Folder Modal -->
<div class="modal fade" id="newFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.file-manager.folder') }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg bg-surface">
            @csrf
            <input type="hidden" name="parent_id" value="{{ request('folder_id') }}">
            <div class="modal-header border-bottom border-subtle p-4">
                <h5 class="modal-title fw-bold text-body"><i class="bi bi-folder-plus text-gold me-2"></i>Create New Directory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="form-label text-secondary small fw-semibold">Folder Name</label>
                <input type="text" name="name" class="form-control bg-body border-subtle text-body rounded-3" placeholder="e.g. Lookbook Autumn 2026" required autofocus>
            </div>
            <div class="modal-footer border-top border-subtle p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-gold rounded-pill px-4 btn-sm fw-bold">Create Folder</button>
            </div>
        </form>
    </div>
</div>

<!-- Rename Folder Modal -->
<div class="modal fade" id="renameFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="renameFolderForm" method="POST" class="modal-content rounded-4 border-0 shadow-lg bg-surface">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom border-subtle p-4">
                <h5 class="modal-title fw-bold text-body"><i class="bi bi-pencil-square text-gold me-2"></i>Rename Directory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="form-label text-secondary small fw-semibold">New Folder Name</label>
                <input type="text" name="name" id="renameFolderName" class="form-control bg-body border-subtle text-body rounded-3" required>
            </div>
            <div class="modal-footer border-top border-subtle p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRenameModal(folderId, currentName) {
    const form = document.getElementById('renameFolderForm');
    form.action = `{{ url('admin/file-manager/folder') }}/${folderId}`;
    document.getElementById('renameFolderName').value = currentName;
    new bootstrap.Modal(document.getElementById('renameFolderModal')).show();
}
</script>
@endsection
