<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MediaAsset;
use App\Services\SecureUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller {
    public function index(Request $request) {
        $tab = $request->query('tab', 'active');
        $search = $request->query('search');
        $type = $request->query('type');
        $sort = $request->query('sort', 'latest');

        $activeCount = MediaAsset::withoutTrashed()->count();
        $trashCount  = MediaAsset::onlyTrashed()->count();

        $query = ($tab === 'trash')
            ? MediaAsset::onlyTrashed()
            : MediaAsset::withoutTrashed();

        if ($search) {
            $query->search($search);
        }

        if ($type && $type !== 'all') {
            $query->ofType($type);
        }

        match ($sort) {
            'oldest'    => $query->oldest('id'),
            'size_desc' => $query->orderByDesc('file_size'),
            'size_asc'  => $query->orderBy('file_size'),
            default     => $query->latest('id'),
        };

        // 24 assets per page = 6 rows of 4 columns
        $mediaAssets = $query->paginate(24)->withQueryString();

        $totalBytes = (int) MediaAsset::withoutTrashed()->sum('file_size');
        $formattedTotalSize = $totalBytes < (1024 * 1024 * 1024)
            ? round($totalBytes / (1024 * 1024), 1) . ' MB'
            : round($totalBytes / (1024 * 1024 * 1024), 2) . ' GB';

        $metrics = [
            'total_assets' => $activeCount,
            'trash_count'  => $trashCount,
            'total_size'   => $formattedTotalSize,
            'jpg_count'    => MediaAsset::withoutTrashed()->where('mime_type', 'like', '%jpeg%')->count(),
            'png_count'    => MediaAsset::withoutTrashed()->where('mime_type', 'like', '%png%')->count(),
            'webp_count'   => MediaAsset::withoutTrashed()->where('mime_type', 'like', '%webp%')->count(),
            'avif_count'   => MediaAsset::withoutTrashed()->where('mime_type', 'like', '%avif%')->count(),
        ];

        return view('admin.media.index', compact(
            'mediaAssets',
            'metrics',
            'tab',
            'activeCount',
            'trashCount',
            'search',
            'type',
            'sort'
        ));
    }

    public function upload(Request $request) {
        // Support both single file ("file") and multiple files ("files")
        if ($request->hasFile('files')) {
            $request->validate([
                'files'    => 'required|array|min:1',
                'files.*'  => 'image|mimes:jpg,jpeg,png,webp,avif|max:15360',
                'alt_text' => 'nullable|string|max:200',
            ]);

            $uploaded = [];
            foreach ($request->file('files') as $file) {
                $asset = SecureUploadService::upload($file, $request->input('alt_text'));
                $uploaded[] = $asset;
            }

            ActivityLog::record('created', "Uploaded " . count($uploaded) . " new media assets to vault", null);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'count'   => count($uploaded),
                    'message' => count($uploaded) . ' media assets uploaded successfully.',
                ]);
            }

            return back()->with('success', count($uploaded) . ' media assets uploaded successfully.');
        }

        $request->validate([
            'file'     => 'required|image|mimes:jpg,jpeg,png,webp,avif|max:15360',
            'alt_text' => 'nullable|string|max:200',
        ]);

        $asset = SecureUploadService::upload($request->file('file'), $request->input('alt_text'));

        ActivityLog::record('created', "Uploaded media asset '{$asset->original_name}'", $asset);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'location' => $asset->url,
                'url'      => $asset->url,
                'id'       => $asset->id,
            ]);
        }

        return back()->with('success', 'Media asset uploaded successfully.');
    }

    /**
     * Soft delete a single media asset (moves to Trash).
     */
    public function destroy(MediaAsset $media) {
        $name = $media->original_name;
        $media->delete();

        ActivityLog::record('deleted', "Moved media asset '{$name}' to trash", null);

        return back()->with('success', "Media asset '{$name}' moved to trash.");
    }

    /**
     * Restore a soft-deleted media asset.
     */
    public function restore($id) {
        $media = MediaAsset::onlyTrashed()->findOrFail($id);
        $name = $media->original_name;
        $media->restore();

        ActivityLog::record('restored', "Restored media asset '{$name}' from trash", $media);

        return back()->with('success', "Media asset '{$name}' restored successfully.");
    }

    /**
     * Permanently erase a soft-deleted media asset from disk and DB.
     */
    public function forceDelete($id) {
        $media = MediaAsset::onlyTrashed()->findOrFail($id);
        $name = $media->original_name;

        // Unlink from storage disk
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        // Unlink from public/storage directory
        $clean = ltrim($media->file_path, '/');
        $pubFile = public_path('storage/' . $clean);
        if (file_exists($pubFile)) {
            @unlink($pubFile);
        }

        $media->forceDelete();

        ActivityLog::record('purged', "Permanently purged media asset '{$name}'", null);

        return back()->with('success', "Media asset '{$name}' permanently erased.");
    }

    /**
     * Multiselect bulk soft-delete.
     */
    public function bulkDestroy(Request $request) {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $count = MediaAsset::withoutTrashed()->whereIn('id', $validated['ids'])->delete();

        ActivityLog::record('deleted', "Moved {$count} media assets to trash in bulk", null);

        return back()->with('success', "{$count} media assets moved to trash.");
    }

    /**
     * Multiselect bulk restore.
     */
    public function bulkRestore(Request $request) {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $count = MediaAsset::onlyTrashed()->whereIn('id', $validated['ids'])->restore();

        ActivityLog::record('restored', "Restored {$count} media assets in bulk", null);

        return back()->with('success', "{$count} media assets restored successfully.");
    }

    /**
     * Multiselect bulk permanent purge.
     */
    public function bulkForceDelete(Request $request) {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $assets = MediaAsset::onlyTrashed()->whereIn('id', $validated['ids'])->get();
        $count = 0;

        foreach ($assets as $media) {
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }
            $clean = ltrim($media->file_path, '/');
            $pubFile = public_path('storage/' . $clean);
            if (file_exists($pubFile)) {
                @unlink($pubFile);
            }
            $media->forceDelete();
            $count++;
        }

        ActivityLog::record('purged', "Permanently purged {$count} media assets in bulk", null);

        return back()->with('success', "{$count} media assets permanently erased.");
    }
}
