<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BackupController extends Controller {
    protected function authorizeAdmin(): void {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized access: The Backups Vault and database archives are restricted exclusively to Administrators.');
        }
    }

    public function index() {
        $this->authorizeAdmin();
        $backups = Backup::latest()->paginate(15);
        return view('admin.backups.index', compact('backups'));
    }

    public function create() {
        $this->authorizeAdmin();
        $backup = BackupService::createDatabaseBackup();
        ActivityLog::record('backup_created', "Manual database backup created: {$backup->filename}", $backup);
        return back()->with('success', 'Database snapshot backup created successfully.');
    }

    public function download(Backup $backup) {
        $this->authorizeAdmin();
        $safeFilename = basename($backup->filename);
        $filePath = storage_path('app/backups/' . $safeFilename);
        if (File::exists($filePath)) {
            ActivityLog::record('backup_downloaded', "Database backup downloaded: {$safeFilename}", $backup);
            return response()->streamDownload(function () use ($filePath) {
                $stream = fopen($filePath, 'rb');
                if ($stream) {
                    fpassthru($stream);
                    fclose($stream);
                }
            }, $safeFilename, [
                'Content-Type' => 'application/x-sqlite3',
            ]);
        }
        return back()->with('error', 'Backup file could not be located on disk.');
    }

    public function destroy(Backup $backup) {
        $this->authorizeAdmin();
        $safeFilename = basename($backup->filename);
        $filePath = storage_path('app/backups/' . $safeFilename);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        $backup->delete();
        ActivityLog::record('backup_deleted', "Database backup purged from disk: {$safeFilename}");
        return back()->with('success', 'Backup archived record purged.');
    }
}
