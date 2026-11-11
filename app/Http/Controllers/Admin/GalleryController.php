<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller {
    public function index(Request $request) {
        $tab = $request->get('tab', 'active');

        $activeCount = Gallery::withoutTrashed()->count();
        $trashCount = Gallery::onlyTrashed()->count();

        if ($tab === 'trash') {
            $galleries = Gallery::onlyTrashed()
                ->withCount('items')
                ->latest('deleted_at')
                ->paginate(15)
                ->withQueryString();
        } else {
            $galleries = Gallery::withoutTrashed()
                ->withCount('items')
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.galleries.index', compact('galleries', 'tab', 'activeCount', 'trashCount'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title'       => 'required|string|max:180',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:active,draft',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'gallery_cover_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('galleries', $filename, 'public');
            $coverPath = '/storage/' . $path;

            // Mirror for non-symlink environments
            $publicDir = public_path('storage/galleries');
            if (!file_exists($publicDir)) @mkdir($publicDir, 0755, true);
            @copy($file->getRealPath(), $publicDir . '/' . $filename);
        }

        $gallery = Gallery::create([
            'title'       => $data['title'],
            'slug'        => Str::slug($data['title']) . '-' . Str::random(5),
            'description' => $data['description'] ?? null,
            'cover_image' => $coverPath,
            'status'      => $data['status'],
        ]);

        if ($request->hasFile('images')) {
            $publicDir = public_path('storage/galleries');
            if (!file_exists($publicDir)) @mkdir($publicDir, 0755, true);

            foreach ($request->file('images') as $idx => $img) {
                $filename = 'gallery_' . $gallery->id . '_' . time() . '_' . $idx . '.' . $img->getClientOriginalExtension();
                $path = $img->storeAs('galleries', $filename, 'public');
                @copy($img->getRealPath(), $publicDir . '/' . $filename);

                GalleryItem::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => '/storage/' . $path,
                    'title'      => $img->getClientOriginalName(),
                    'order'      => $idx,
                ]);
            }
        }

        ActivityLog::record('created', "Created gallery '{$gallery->title}'", $gallery);

        return back()->with('success', 'Maison Gallery established successfully.');
    }

    public function show(Gallery $gallery) {
        $gallery->load('items');
        return view('admin.galleries.show', compact('gallery'));
    }

    public function addItems(Request $request, Gallery $gallery) {
        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:15360',
        ]);

        $publicDir = public_path('storage/galleries');
        if (!file_exists($publicDir)) @mkdir($publicDir, 0755, true);

        $currentMax = (int) $gallery->items()->max('order');
        $uploadedCount = 0;

        foreach ($request->file('images') as $idx => $img) {
            $filename = 'gallery_' . $gallery->id . '_' . time() . '_' . $idx . '.' . $img->getClientOriginalExtension();
            $path = $img->storeAs('galleries', $filename, 'public');
            @copy($img->getRealPath(), $publicDir . '/' . $filename);

            GalleryItem::create([
                'gallery_id' => $gallery->id,
                'image_path' => '/storage/' . $path,
                'title'      => $img->getClientOriginalName(),
                'order'      => $currentMax + $idx + 1,
            ]);
            $uploadedCount++;
        }

        if (empty($gallery->cover_image) && $gallery->items()->exists()) {
            $gallery->update(['cover_image' => $gallery->items()->first()->image_path]);
        }

        ActivityLog::record('updated', "Added {$uploadedCount} new photos to gallery '{$gallery->title}'", $gallery);

        return back()->with('success', "Added {$uploadedCount} new photos to gallery successfully.");
    }

    public function setCover(Gallery $gallery, GalleryItem $item) {
        if ($item->gallery_id !== $gallery->id) {
            abort(403);
        }

        $gallery->update(['cover_image' => $item->image_path]);

        ActivityLog::record('updated', "Updated cover photograph for gallery '{$gallery->title}'", $gallery);

        return back()->with('success', 'Cover photograph updated successfully.');
    }

    public function destroyItem(Gallery $gallery, GalleryItem $item) {
        if ($item->gallery_id !== $gallery->id) {
            abort(403);
        }

        $wasCover = ($gallery->cover_image === $item->image_path);

        $clean = ltrim($item->image_path, '/');
        $pubPath = public_path($clean);
        if (file_exists($pubPath)) {
            @unlink($pubPath);
        }

        $item->delete();

        if ($wasCover) {
            $nextItem = $gallery->items()->first();
            $gallery->update(['cover_image' => $nextItem ? $nextItem->image_path : null]);
        }

        ActivityLog::record('deleted', "Removed photo from gallery '{$gallery->title}'", $gallery);

        return back()->with('success', 'Photograph removed from gallery.');
    }

    public function update(Request $request, Gallery $gallery) {
        $data = $request->validate([
            'title'       => 'required|string|max:180',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:active,draft',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'gallery_cover_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('galleries', $filename, 'public');
            $data['cover_image'] = '/storage/' . $path;

            $publicDir = public_path('storage/galleries');
            if (!file_exists($publicDir)) @mkdir($publicDir, 0755, true);
            @copy($file->getRealPath(), $publicDir . '/' . $filename);
        }

        $gallery->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? $gallery->description,
            'cover_image' => $data['cover_image'] ?? $gallery->cover_image,
            'status'      => $data['status'],
        ]);

        if ($request->hasFile('images')) {
            $publicDir = public_path('storage/galleries');
            if (!file_exists($publicDir)) @mkdir($publicDir, 0755, true);

            $currentMax = (int) $gallery->items()->max('order');
            foreach ($request->file('images') as $idx => $img) {
                $filename = 'gallery_' . $gallery->id . '_' . time() . '_' . $idx . '.' . $img->getClientOriginalExtension();
                $path = $img->storeAs('galleries', $filename, 'public');
                @copy($img->getRealPath(), $publicDir . '/' . $filename);

                GalleryItem::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => '/storage/' . $path,
                    'title'      => $img->getClientOriginalName(),
                    'order'      => $currentMax + $idx + 1,
                ]);
            }
        }

        ActivityLog::record('updated', "Updated gallery '{$gallery->title}'", $gallery);

        return back()->with('success', 'Gallery record updated successfully.');
    }

    public function destroy(Gallery $gallery) {
        $title = $gallery->title;
        $gallery->delete();

        ActivityLog::record('deleted', "Archived gallery '{$title}' to Trash", $gallery);

        return back()->with('success', "Gallery '{$title}' moved to trash.");
    }

    public function restore($id) {
        $gallery = Gallery::onlyTrashed()->findOrFail($id);
        $gallery->restore();

        ActivityLog::record('restored', "Restored gallery '{$gallery->title}' from Trash", $gallery);

        return back()->with('success', "Gallery '{$gallery->title}' restored successfully.");
    }

    public function forceDelete($id) {
        $gallery = Gallery::onlyTrashed()->findOrFail($id);
        $title = $gallery->title;
        $gallery->items()->delete();
        $gallery->forceDelete();

        ActivityLog::record('purged', "Permanently purged gallery '{$title}'", null);

        return back()->with('success', "Gallery '{$title}' permanently erased.");
    }
}
