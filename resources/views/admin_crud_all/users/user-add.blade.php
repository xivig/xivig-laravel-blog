@extends('admin.layouts.admin')

@section('title', isset($duplicate) ? 'Duplicate User Account' : 'Create User Account')
@section('page_title', isset($duplicate) ? 'Duplication Mode' : 'New User Account')
@section('parentTitle', 'User Directory')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        @if(isset($original))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
            <div class="icon-shape bg-warning text-dark rounded-circle p-2">
                <i class="bi bi-files fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">Duplication Mode Active</h6>
                <p class="mb-0 small">You are creating an account template cloned from <strong>{{ $original->name }}</strong>. Please ensure the mandatory fields (unique email, initial password) are supplied below.</p>
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-card">
            <div class="card-header bg-transparent py-4 px-4 px-md-5 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="fw-black text-body mb-1">{{ isset($duplicate) ? 'Clone User Profile' : 'New User Profile' }}</h4>
                    <p class="text-muted small mb-0">{{ isset($duplicate) ? 'Adjust cloned parameters before writing to the database' : 'Provide credential and authorization details for the new account' }}</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Directory
                </a>
            </div>

            <div class="card-body p-4 p-md-5 pt-0">
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" value="{{ old('name', $duplicate->name ?? '') }}" placeholder="e.g. Sebastian Moreau" required>
                            </div>
                            @error('name')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">
                                Email Address <span class="text-danger">*</span>
                                @if(isset($mandatory) && in_array('email', $mandatory))
                                    <span class="badge rounded-pill bg-warning text-dark ms-2 extra-small">MUST BE UNIQUE</span>
                                @endif
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email', $duplicate->email ?? '') }}" placeholder="e.g. sebastian@xivig.com" required>
                            </div>
                            @error('email')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- User Role -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">System Role &amp; Clearance <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="user" {{ old('role', $duplicate->role ?? 'author') == 'user' ? 'selected' : '' }}>Patron User (Discourse &amp; Comments Only)</option>
                                <option value="author" {{ old('role', $duplicate->role ?? 'author') == 'author' ? 'selected' : '' }}>Author (Draft Chronicles &amp; Submissions)</option>
                                <option value="editor" {{ old('role', $duplicate->role ?? 'author') == 'editor' ? 'selected' : '' }}>Editor (Review, Curate &amp; Publish)</option>
                                <option value="admin" {{ old('role', $duplicate->role ?? 'author') == 'admin' ? 'selected' : '' }}>Administrator (Unrestricted Atelier Access)</option>
                            </select>
                            @error('role')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">
                                Secret Passkey <span class="text-danger">*</span>
                                @if(isset($mandatory) && in_array('password', $mandatory))
                                    <span class="badge rounded-pill bg-warning text-dark ms-2 extra-small">REQUIRED</span>
                                @endif
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                            </div>
                            @error('password')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Designation -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Job Designation</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-briefcase"></i></span>
                                <input type="text" name="designation" class="form-control border-start-0 ps-0" value="{{ old('designation', $duplicate->designation ?? '') }}" placeholder="e.g. Senior Fashion Critic">
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone', $duplicate->phone ?? '') }}" placeholder="+1 (555) 019-2834">
                            </div>
                        </div>

                        <!-- Profile Avatar Upload -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Profile Portrait / Avatar</label>
                            <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                            <div class="form-text extra-small text-muted">Supports JPG, PNG, WEBP, GIF up to 2MB. Square ratio recommended.</div>
                            @error('avatar')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Biography -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Biography &amp; Atelier Notes</label>
                            <textarea name="bio" class="form-control" rows="4" placeholder="Brief biographical introduction or editorial accreditation notes...">{{ old('bio', $duplicate->bio ?? '') }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 border-top pt-4 mt-4 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-gold rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                {{ isset($duplicate) ? 'Confirm Duplication' : 'Create Account' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
