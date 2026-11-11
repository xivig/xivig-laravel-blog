@extends('frontend.layouts.app')

@section('title', 'Protected Chronicle | ' . $globalSettings['title'])
@section('meta_description', 'This chronicle is protected by Maison Salon Codex.')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="container py-5 my-md-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="bento-card p-4 p-md-5 text-center shadow-lg border border-subtle">
                <div class="brand-monogram mx-auto mb-4" style="width: 64px; height: 64px;">
                    <i class="bi bi-shield-lock-fill text-gold fs-3"></i>
                </div>

                <span class="badge bg-gold-subtle text-gold text-uppercase letter-spacing-2 extra-small px-3 py-1 rounded-pill mb-3">
                    Salon Codex Privé
                </span>

                <h3 class="fw-bold mb-2">Chronicle Protected</h3>
                <p class="text-secondary small mb-4">
                    The chronicle &ldquo;<strong>{{ $post->title }}</strong>&rdquo; is reserved for patrons possessing the atelier passphrase.
                </p>

                @if(session('error'))
                    <div class="alert alert-danger rounded-3 py-2 small mb-4 d-flex align-items-center gap-2 text-start">
                        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('blog.unlock', $post->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="postPassword" class="form-label extra-small text-uppercase fw-bold letter-spacing-1 text-secondary">
                            Atelier Passphrase
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-body border-subtle text-gold">
                                <i class="bi bi-key-fill"></i>
                            </span>
                            <input type="password" id="postPassword" name="password" required autofocus class="form-control bg-body border-subtle shadow-none py-2" placeholder="Enter passphrase...">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-2 fw-bold text-uppercase letter-spacing-1 shadow-sm">
                        <i class="bi bi-unlock-fill me-1"></i> Unlock Chronicle
                    </button>
                </form>

                <div class="pt-3 border-top border-subtle">
                    <a href="{{ route('blog.index') }}" class="text-secondary small text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Return to All Chronicles
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
