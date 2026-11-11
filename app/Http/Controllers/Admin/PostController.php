<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Revision;
use App\Models\Tag;
use App\Services\HtmlSanitizerService;
use App\Services\SecureUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller {
    public function index(Request $request) {
        $query = Post::with(['author', 'category', 'tags'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }
        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where('title', 'like', "%{$s}%");
        }

        $posts = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    public function create() {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('authors', 'categories', 'tags'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title'                  => 'required|string|max:255',
            'slug'                   => 'nullable|string|max:255|unique:posts,slug',
            'author_id'              => 'nullable|exists:authors,id',
            'category_id'            => 'nullable|exists:categories,id',
            'excerpt'                => 'nullable|string|max:600',
            'content'                => 'required|string',
            'featured_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:5120',
            'featured_image_caption' => 'nullable|string|max:255',
            'featured_image_alt'     => 'nullable|string|max:255',
            'status'                 => 'required|in:draft,published,scheduled,archived',
            'is_featured'            => 'nullable|boolean',
            'is_editor_pick'         => 'nullable|boolean',
            'is_sticky'              => 'nullable|boolean',
            'is_private'             => 'nullable|boolean',
            'password'               => 'nullable|string|max:100',
            'scheduled_at'           => 'nullable|date',
            'meta_title'             => 'nullable|string|max:255',
            'meta_description'       => 'nullable|string|max:400',
            'meta_keywords'          => 'nullable|string|max:255',
            'canonical_url'          => 'nullable|url|max:255',
            'tags'                   => 'nullable|array',
            'tags.*'                 => 'exists:tags,id',
            'gallery_images'         => 'nullable|array',
            'gallery_images.*'       => 'image|mimes:jpg,jpeg,png,webp,avif|max:5120',
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['title']);
        $uniqueSlug = $slug;
        $count = 1;
        while (Post::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $count++;
        }

        // Sanitize article content
        $cleanContent = HtmlSanitizerService::cleanArticle($data['content']);

        // Compute Reading Time (approx 200 words per minute)
        $wordCount = str_word_count(strip_tags($cleanContent));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $media = SecureUploadService::upload($request->file('featured_image'), $data['title']);
            $imagePath = $media->file_path;
        }

        $publishedAt = null;
        if ($data['status'] === 'published') {
            $publishedAt = now();
        } elseif ($data['status'] === 'scheduled' && !empty($data['scheduled_at'])) {
            $publishedAt = Carbon::parse($data['scheduled_at']);
        }

        $post = Post::create([
            'title'                  => $data['title'],
            'slug'                   => $uniqueSlug,
            'author_id'              => $data['author_id'] ?? null,
            'category_id'            => $data['category_id'] ?? null,
            'excerpt'                => $data['excerpt'] ?? Str::limit(strip_tags($cleanContent), 180),
            'content'                => $cleanContent,
            'featured_image'         => $imagePath,
            'featured_image_caption' => $data['featured_image_caption'] ?? null,
            'featured_image_alt'     => $data['featured_image_alt'] ?? null,
            'status'                 => $data['status'],
            'is_featured'            => (bool) ($data['is_featured'] ?? false),
            'is_editor_pick'         => (bool) ($data['is_editor_pick'] ?? false),
            'is_sticky'              => (bool) ($data['is_sticky'] ?? false),
            'is_private'             => (bool) ($data['is_private'] ?? false),
            'password'               => !empty($data['password']) ? $data['password'] : null,
            'reading_time'           => $readingTime,
            'published_at'           => $publishedAt,
            'meta_title'             => $data['meta_title'] ?? $data['title'],
            'meta_description'       => $data['meta_description'] ?? null,
            'meta_keywords'          => $data['meta_keywords'] ?? null,
            'canonical_url'          => $data['canonical_url'] ?? null,
        ]);

        if (!empty($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        // Unlimited Gallery Images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $idx => $file) {
                $media = SecureUploadService::upload($file, $post->title . ' Gallery ' . ($idx + 1));
                $post->galleryImages()->create([
                    'image_path' => $media->file_path,
                    'caption'    => $request->input('gallery_captions.' . $idx, null),
                    'alt_text'   => $post->title,
                    'order'      => $idx + 1,
                ]);
            }
        }

        \App\Models\ActivityLog::record('created', "Created editorial story: \"{$post->title}\"", $post);

        return redirect()->route('admin.posts.index')->with('success', 'Editorial story created successfully.');
    }

    public function edit(Post $post) {
        $post->load('galleryImages');
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'authors', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post) {
        $data = $request->validate([
            'title'                  => 'required|string|max:255',
            'slug'                   => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
            'author_id'              => 'nullable|exists:authors,id',
            'category_id'            => 'nullable|exists:categories,id',
            'excerpt'                => 'nullable|string|max:600',
            'content'                => 'required|string',
            'featured_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:5120',
            'featured_image_caption' => 'nullable|string|max:255',
            'featured_image_alt'     => 'nullable|string|max:255',
            'status'                 => 'required|in:draft,published,scheduled,archived',
            'is_featured'            => 'nullable|boolean',
            'is_editor_pick'         => 'nullable|boolean',
            'is_sticky'              => 'nullable|boolean',
            'is_private'             => 'nullable|boolean',
            'password'               => 'nullable|string|max:100',
            'scheduled_at'           => 'nullable|date',
            'meta_title'             => 'nullable|string|max:255',
            'meta_description'       => 'nullable|string|max:400',
            'meta_keywords'          => 'nullable|string|max:255',
            'canonical_url'          => 'nullable|url|max:255',
            'tags'                   => 'nullable|array',
            'tags.*'                 => 'exists:tags,id',
            'gallery_images'         => 'nullable|array',
            'gallery_images.*'       => 'image|mimes:jpg,jpeg,png,webp,avif|max:5120',
        ]);

        $cleanContent = HtmlSanitizerService::cleanArticle($data['content']);
        $wordCount = str_word_count(strip_tags($cleanContent));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        if ($request->hasFile('featured_image')) {
            $media = SecureUploadService::upload($request->file('featured_image'), $data['title']);
            $post->featured_image = $media->file_path;
        }

        $post->title = $data['title'];
        if (!empty($data['slug'])) {
            $post->slug = Str::slug($data['slug']);
        }
        $post->author_id = $data['author_id'] ?? null;
        $post->category_id = $data['category_id'] ?? null;
        $post->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($cleanContent), 180);
        $post->content = $cleanContent;
        $post->featured_image_caption = $data['featured_image_caption'] ?? null;
        $post->featured_image_alt = $data['featured_image_alt'] ?? null;
        $post->status = $data['status'];
        $post->is_featured = (bool) ($data['is_featured'] ?? false);
        $post->is_editor_pick = (bool) ($data['is_editor_pick'] ?? false);
        $post->is_sticky = (bool) ($data['is_sticky'] ?? false);
        $post->is_private = (bool) ($data['is_private'] ?? false);
        $post->password = !empty($data['password']) ? $data['password'] : null;
        $post->reading_time = $readingTime;
        $post->meta_title = $data['meta_title'] ?? $data['title'];
        $post->meta_description = $data['meta_description'] ?? null;
        $post->meta_keywords = $data['meta_keywords'] ?? null;
        $post->canonical_url = $data['canonical_url'] ?? null;

        if ($post->status === 'published' && !$post->published_at) {
            $post->published_at = now();
        } elseif ($post->status === 'scheduled' && !empty($data['scheduled_at'])) {
            $post->published_at = Carbon::parse($data['scheduled_at']);
        }

        $post->save();

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        // Unlimited Gallery Images
        if ($request->hasFile('gallery_images')) {
            $currentMaxOrder = (int) $post->galleryImages()->max('order');
            foreach ($request->file('gallery_images') as $idx => $file) {
                $media = SecureUploadService::upload($file, $post->title . ' Gallery ' . ($currentMaxOrder + $idx + 1));
                $post->galleryImages()->create([
                    'image_path' => $media->file_path,
                    'caption'    => $request->input('gallery_captions.' . $idx, null),
                    'alt_text'   => $post->title,
                    'order'      => $currentMaxOrder + $idx + 1,
                ]);
            }
        }

        \App\Models\ActivityLog::record('updated', "Updated editorial story: \"{$post->title}\"", $post);

        return redirect()->route('admin.posts.index')->with('success', 'Editorial story updated.');
    }

    public function destroy(Post $post) {
        \App\Models\ActivityLog::record('deleted', "Deleted editorial story: \"{$post->title}\"", $post);
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Story moved to trash.');
    }

    public function toggleStatus(Post $post) {
        $post->status = $post->status === 'published' ? 'draft' : 'published';
        if ($post->status === 'published' && !$post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        \App\Models\ActivityLog::record('updated', "Toggled story status to {$post->status}: \"{$post->title}\"", $post);

        return back()->with('success', 'Status toggled to ' . ucfirst($post->status));
    }

    public function clone(Post $post) {
        $clone = $post->replicate();
        $clone->title = $post->title . ' (Copy)';
        $clone->slug = Str::slug($post->title) . '-' . Str::lower(Str::random(5));
        $clone->status = 'draft';
        $clone->is_sticky = false;
        $clone->views_count = 0;
        $clone->likes_count = 0;
        $clone->published_at = null;
        $clone->save();

        $clone->tags()->sync($post->tags->pluck('id'));

        foreach ($post->galleryImages as $img) {
            $clone->galleryImages()->create([
                'image_path' => $img->image_path,
                'caption'    => $img->caption,
                'alt_text'   => $img->alt_text,
                'order'      => $img->order,
            ]);
        }

        \App\Models\ActivityLog::record('created', "Cloned editorial story: \"{$clone->title}\"", $clone);

        return redirect()->route('admin.posts.edit', $clone->id)->with('success', 'Story duplicated as draft.');
    }

    public function preview(Post $post) {
        return redirect()->route('blog.show', [$post->slug, 'preview' => '1']);
    }

    public function deleteGalleryImage(PostImage $image) {
        $image->delete();
        return back()->with('success', 'Gallery asset removed.');
    }

    public function revisions(Post $post) {
        $revisions = $post->revisions()->with('user')->get();
        return view('admin.posts.revisions', compact('post', 'revisions'));
    }

    public function restoreRevision(Post $post, Revision $revision) {
        if ($revision->revisionable_id != $post->id || $revision->revisionable_type !== Post::class) {
            return back()->with('error', 'The requested snapshot does not belong to this article.');
        }

        // Archive current state prior to restoration
        try {
            Revision::create([
                'user_id'           => auth()->id(),
                'revisionable_type' => Post::class,
                'revisionable_id'   => $post->id,
                'title'             => $post->title,
                'content'           => $post->content,
                'summary'           => 'Snapshot captured prior to revision restoration',
            ]);
        } catch (\Throwable $e) {}

        $post->title = $revision->title;
        $post->content = $revision->content;
        $post->save();

        ActivityLog::record('post_revision_restore', "Post \"{$post->title}\" rolled back to revision snapshot #{$revision->id}", $post);

        return redirect()->route('admin.posts.edit', $post->id)
            ->with('success', "Article successfully restored to snapshot from " . ($revision->created_at ? $revision->created_at->format('M d, Y H:i') : 'historical revision') . ".");
    }
}
