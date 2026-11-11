@extends('admin.layouts.admin')

@section('title', 'Edit User: ' . $user->name)
@section('page_title', 'Edit User Account')
@section('parentTitle', 'User Directory')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-card">
            <div class="card-header bg-transparent py-4 px-4 px-md-5 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    @if($user->profile_picture)
                        <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 52px; height: 52px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary-subtle text-primary fw-black d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; font-size: 1.3rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="fw-black text-body mb-0">{{ $user->name }}</h4>
                        <p class="text-muted small mb-0">{{ $user->email }} &bull; Role: <strong class="text-gold">{{ ucfirst($user->role) }}</strong></p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-info btn-sm rounded-pill px-3">
                        <i class="bi bi-eye me-1"></i> View Details
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body p-4 p-md-5 pt-0">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            </div>
                            @error('name')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            </div>
                            @error('email')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">System Role &amp; Clearance <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Patron User (Discourse &amp; Comments Only)</option>
                                <option value="author" {{ old('role', $user->role) == 'author' ? 'selected' : '' }}>Author (Draft Chronicles &amp; Submissions)</option>
                                <option value="editor" {{ old('role', $user->role) == 'editor' ? 'selected' : '' }}>Editor (Review, Curate &amp; Publish)</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator (Unrestricted Atelier Access)</option>
                            </select>
                            @error('role')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Secret Passkey</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="Leave blank to keep existing password">
                            </div>
                            <div class="form-text extra-small text-muted">Only fill if you want to reset this user's password.</div>
                            @error('password')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Designation -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Job Designation</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-briefcase"></i></span>
                                <input type="text" name="designation" class="form-control border-start-0 ps-0" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Senior Fashion Critic">
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 019-2834">
                            </div>
                        </div>

                        <!-- Avatar -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Profile Portrait / Avatar</label>
                            <div class="d-flex align-items-center gap-3">
                                @if($user->profile_picture)
                                    <img src="{{ $user->profile_picture }}" alt="Current Avatar" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                @endif
                                <div class="flex-grow-1">
                                    <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                                    <div class="form-text extra-small text-muted">Upload a new picture to replace the current one. Supports JPG, PNG, WEBP, GIF up to 2MB.</div>
                                </div>
                            </div>
                            @error('avatar')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Biography -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Biography &amp; Atelier Notes</label>
                            <textarea name="bio" class="form-control" rows="4">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 border-top pt-4 mt-4 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-gold rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save2-fill me-1"></i> Update Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
