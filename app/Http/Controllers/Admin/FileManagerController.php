<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FileManagerFile;
use App\Models\FileManagerFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileManagerController extends Controller {
    public function index(Request $request) {
        $folderId = $request->get('folder_id');
        $status = $request->get('status');
        $search = $request->get('search');

        $folderQuery = FileManagerFolder::query();
        $fileQuery = FileManagerFile::query();

        if ($status === 'trash') {
            $folderQuery->onlyTrashed();
            $fileQuery->onlyTrashed();
        } else {
            if (!$search) {
                $folderQuery->where('parent_id', $folderId);
                $fileQuery->where('folder_id', $folderId);
            }
        }

        if ($search) {
            $folderQuery->where('name', 'like', "%{$search}%");
            $fileQuery->where('name', 'like', "%{$search}%");
        }

        $folders = $folderQuery->latest()->get();
        $files = $fileQuery->latest()->get();

        $currentFolder = $folderId ? FileManagerFolder::withTrashed()->find($folderId) : null;

        // Statistics
        $totalFilesCount = FileManagerFile::count();
        $totalFoldersCount = FileManagerFolder::count();

        return view('admin.apps.file-manager', compact(
            'folders',
            'files',
            'currentFolder',
            'status',
            'totalFilesCount',
            'totalFoldersCount'
        ));
    }

    public function createFolder(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);

        $colors = ['primary', 'warning', 'info', 'success', 'danger'];
        $color = $colors[array_rand($colors)];

        $folder = FileManagerFolder::create([
            'name'      => $request->name,
            'parent_id' => $request->parent_id,
            'color'     => $color,
        ]);

        ActivityLog::record('created', "Created directory folder '{$folder->name}'", $folder);

        return back()->with('success', 'Folder created successfully.');
    }

    public function renameFolder(Request $request, $id) {
        $request->validate(['name' => 'required|string|max:255']);
        $folder = FileManagerFolder::findOrFail($id);
        $folder->update(['name' => $request->name]);

        ActivityLog::record('updated', "Renamed folder to '{$folder->name}'", $folder);

        return back()->with('success', 'Folder renamed.');
    }

    public function deleteFolder($id) {
        $folder = FileManagerFolder::findOrFail($id);
        $folder->delete();

        ActivityLog::record('deleted', "Moved folder '{$folder->name}' to trash", $folder);

        return back()->with('success', 'Folder moved to trash.');
    }

    public function restoreFolder($id) {
        $folder = FileManagerFolder::withTrashed()->findOrFail($id);
        $folder->restore();

        ActivityLog::record('restored', "Restored folder '{$folder->name}'", $folder);

        return back()->with('success', 'Folder restored.');
    }

    public function forceDeleteFolder($id) {
        $folder = FileManagerFolder::withTrashed()->findOrFail($id);
        $name = $folder->name;
        $folder->forceDelete();

        ActivityLog::record('purged', "Permanently destroyed folder '{$name}'");

        return back()->with('success', 'Folder permanently deleted.');
    }

    public function uploadFile(Request $request) {
        $request->validate([
            'file' => 'required|file|max:32768', // Max 32MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();

            if ($size >= 1048576) {
                $formattedSize = number_format($size / 1048576, 2) . ' MB';
            } else {
                $formattedSize = number_format($size / 1024, 1) . ' KB';
            }

            $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $filename = time() . '_' . $cleanName . '.' . $ext;

            // Store via Laravel filesystem disk
            $file->storeAs('uploads', $filename, 'public');

            $icon = 'bi-file-earmark';
            $color = 'primary';

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $icon = 'bi-file-earmark-image-fill';
                $color = 'info';
            } elseif ($ext === 'pdf') {
                $icon = 'bi-file-earmark-pdf-fill';
                $color = 'danger';
            } elseif (in_array($ext, ['doc', 'docx'])) {
                $icon = 'bi-file-earmark-word-fill';
                $color = 'primary';
            } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                $icon = 'bi-file-earmark-excel-fill';
                $color = 'success';
            } elseif (in_array($ext, ['zip', 'rar', 'tar', 'gz', '7z'])) {
                $icon = 'bi-file-earmark-zip-fill';
                $color = 'warning';
            } elseif (in_array($ext, ['mp3', 'wav', 'ogg'])) {
                $icon = 'bi-file-earmark-music-fill';
                $color = 'info';
            } elseif (in_array($ext, ['mp4', 'mov', 'webm', 'avi'])) {
                $icon = 'bi-file-earmark-play-fill';
                $color = 'danger';
            }

            $asset = FileManagerFile::create([
                'name'      => $originalName,
                'path'      => 'storage/uploads/' . $filename,
                'type'      => $ext,
                'size'      => $formattedSize,
                'icon'      => $icon,
                'color'     => $color,
                'folder_id' => $request->folder_id,
            ]);

            ActivityLog::record('uploaded', "Uploaded archive asset '{$originalName}'", $asset);

            return back()->with('success', 'File uploaded successfully.');
        }

        return back()->with('error', 'No file selected for upload.');
    }

    public function deleteFile($id) {
        $file = FileManagerFile::findOrFail($id);
        $file->delete();

        ActivityLog::record('deleted', "Moved file '{$file->name}' to trash", $file);

        return back()->with('success', 'File moved to trash.');
    }

    public function restoreFile($id) {
        $file = FileManagerFile::withTrashed()->findOrFail($id);
        $file->restore();

        ActivityLog::record('restored', "Restored file '{$file->name}'", $file);

        return back()->with('success', 'File restored.');
    }

    public function forceDeleteFile($id) {
        $file = FileManagerFile::withTrashed()->findOrFail($id);
        $filePath = public_path($file->path);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        $name = $file->name;
        $file->forceDelete();

        ActivityLog::record('purged', "Permanently destroyed file '{$name}'");

        return back()->with('success', 'File permanently deleted.');
    }
}
