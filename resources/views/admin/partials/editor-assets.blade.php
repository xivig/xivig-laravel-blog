{{-- WYSIWYG Editor Assets (TinyMCE 7 with Local Summernote-BS5 Fallback) --}}
<link rel="stylesheet" href="{{ asset('vendor/summernote/summernote-bs5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/wysiwyg.css') }}">

{{-- TinyMCE 7 CDN (non-blocking) --}}
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>

{{-- Local Summernote-BS5 JS & Unified Editor Engine --}}
<script src="{{ asset('vendor/summernote/summernote-bs5.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/wysiwyg.js') }}"></script>
