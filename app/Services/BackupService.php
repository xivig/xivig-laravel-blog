<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\File;

class BackupService {
    public static function createDatabaseBackup(): Backup {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'backup_xivig_' . date('Y_m_d_His') . '.sqlite';
        $destination = $backupDir . '/' . $filename;

        $dbPath = database_path('database.sqlite');
        if (File::exists($dbPath)) {
            File::copy($dbPath, $destination);
            $size = File::size($destination);
        } else {
            File::put($destination, '-- XIVIG Backup generated at ' . now());
            $size = strlen('-- XIVIG Backup generated at ' . now());
        }

        return Backup::create([
            'filename'   => $filename,
            'disk'       => 'local',
            'type'       => 'database',
            'size_bytes' => $size,
            'status'     => 'completed',
        ]);
    }
}
