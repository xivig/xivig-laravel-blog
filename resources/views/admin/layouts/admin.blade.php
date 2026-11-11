<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    @include('admin.partials.head')
</head>
<body class="d-flex flex-column min-vh-100" data-bs-spy="scroll">

@include('admin.partials.preloader')
@include('admin.partials.header')
@include('admin.partials.sidebar')
@include('admin.partials.right-sidebar')

<main class="main mt-5">
    <div class="main__container container-fluid">

        <!-- Standardized Page Header -->
        <div class="page-header shadow-sm mb-4">
            <h2 class="page-header__title">@yield('page_title', View::yieldContent('title', 'Admin Desk'))</h2>
            <nav class="page-header__nav" aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Home</a>
                    </li>
                    @hasSection('parentTitle')
                        <li class="breadcrumb-item">
                            <a href="#">@yield('parentTitle')</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">@yield('page_title', View::yieldContent('title', 'Dashboard'))</li>
                </ol>
            </nav>
        </div>

        <!-- System Alerts & Flash Feedback -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-3 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-x-octagon-fill fs-5 me-2 text-danger"></i>
                    <strong>Please review the following issues:</strong>
                </div>
                <ul class="mb-0 small ps-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content Area -->
        @yield('content')

    </div>
</main>

@include('admin.partials.footer')
@include('admin.partials.scripts')

</body>
</html>
