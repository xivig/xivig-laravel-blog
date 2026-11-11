@extends('admin.layouts.admin')

@section('title', 'Search Results: ' . $query . ' | Admin Desk')
@section('page_title', 'Search Results')

@section('content')
<div class="row g-4">
    <!-- Header Banner -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-2 bg-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Search Results for: <span class="text-primary">"{{ $query }}"</span></h4>
                    <p class="text-muted small mb-0">Found matches across stories, taxonomies, comments, inquiries, and static pages.</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Shortcuts (If Any) -->
    @if(!empty($shortcuts))
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted d-block mb-2">
                <i class="bi bi-lightning-charge text-gold me-1"></i> Quick Jumps
            </span>
            <div class="d-flex flex-wrap gap-2">
                @foreach($shortcuts as $sc)
                    <a href="{{ $sc['url'] }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-2">
                        <i class="bi {{ $sc['icon'] }} text-primary"></i>
                        <span>{{ $sc['title'] }}</span>
                        <span class="badge bg-body-secondary text-muted extra-small py-0.5 px-2">{{ $sc['badge'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Articles Matches -->
    <div class="col-lg-8">
        <!-- Stories -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-body">
            <div class="card-header bg-transparent py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-journal-richtext text-primary me-2"></i>Articles &amp; Stories ({{ $posts->total() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Article</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-body d-block">{{ $post->title }}</span>
                                <span class="extra-small text-muted">{{ $post->slug }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $post->category ? $post->category->name : 'General' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $post->status === 'published' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill">
                                    {{ ucfirst($post->status) }}
                                </span>
                            </td>
                            <td>{{ number_format($post->views_count) }}</td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">Edit</a>
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-light border rounded-circle" title="View live">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No articles found matching "{{ $query }}".</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($posts->hasPages())
                <div class="card-footer bg-transparent py-3 border-top d-flex justify-content-center">
                    {{ $posts->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <!-- Matching Static Pages -->
        @if(isset($pages) && $pages->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-body">
            <div class="card-header bg-transparent py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-file-earmark-text text-info me-2"></i>Static Pages ({{ $pages->count() }})
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($pages as $page)
                <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                    <div>
                        <span class="fw-bold text-body d-block">{{ $page->title }}</span>
                        <span class="extra-small text-muted font-monospace">/{{ $page->slug }}</span>
                    </div>
                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                        Edit Page
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Reader Comments Matches -->
        @if(isset($comments) && $comments->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-body">
            <div class="card-header bg-transparent py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-chat-dots text-warning me-2"></i>Reader Comments ({{ $comments->count() }})
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($comments as $comment)
                <div class="list-group-item py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <span class="fw-bold text-body small">{{ $comment->author_name }}</span>
                            <span class="extra-small text-muted ms-2">{{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}</span>
                        </div>
                        <span class="badge {{ $comment->status === 'approved' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill extra-small">
                            {{ ucfirst($comment->status) }}
                        </span>
                    </div>
                    <p class="mb-1 text-muted small">{{ Str::limit($comment->content, 120) }}</p>
                    <a href="{{ route('admin.comments.index') }}" class="extra-small text-primary text-decoration-none fw-semibold">
                        View in Comments &rarr;
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Contact Inquiries Matches -->
        @if(isset($contacts) && $contacts->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-body">
            <div class="card-header bg-transparent py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-envelope-paper text-success me-2"></i>Contact Inquiries ({{ $contacts->count() }})
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($contacts as $contact)
                <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                    <div>
                        <div class="fw-bold text-body small">{{ $contact->name }} <span class="text-muted fw-normal">({{ $contact->email }})</span></div>
                        <span class="extra-small text-muted">{{ $contact->subject ?: 'General Inquiry' }}</span>
                    </div>
                    <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        View Inquiry
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Side Taxonomy & Audience Matches -->
    <div class="col-lg-4">
        <!-- Categories -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Categories ({{ $categories->count() }})</h6>
                <a href="{{ route('admin.categories.index') }}" class="extra-small text-primary text-decoration-none">Manage &rarr;</a>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @forelse($categories as $cat)
                    <a href="{{ route('admin.categories.index') }}" class="badge bg-primary-subtle text-primary p-2 px-3 rounded-pill text-decoration-none">
                        <i class="bi bi-folder2 me-1"></i>{{ $cat->name }}
                    </a>
                @empty
                    <span class="text-muted extra-small">No categories found.</span>
                @endforelse
            </div>
        </div>

        <!-- Tags -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Tags &amp; Topics ({{ $tags->count() }})</h6>
                <a href="{{ route('admin.tags.index') }}" class="extra-small text-primary text-decoration-none">Manage &rarr;</a>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @forelse($tags as $t)
                    <a href="{{ route('admin.tags.index') }}" class="badge bg-secondary-subtle text-secondary p-2 px-3 rounded-pill text-decoration-none">
                        #{{ $t->name }}
                    </a>
                @empty
                    <span class="text-muted extra-small">No tags found.</span>
                @endforelse
            </div>
        </div>

        <!-- Subscribers -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Subscribers ({{ $subscribers->count() }})</h6>
                <a href="{{ route('admin.subscribers.index') }}" class="extra-small text-primary text-decoration-none">Manage &rarr;</a>
            </div>
            <ul class="list-unstyled mb-0 extra-small">
                @forelse($subscribers as $sub)
                    <li class="py-1.5 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-truncate me-2">{{ $sub->email }}</span>
                        <span class="badge bg-success-subtle text-success">Active</span>
                    </li>
                @empty
                    <li class="text-muted">No subscribers found.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
