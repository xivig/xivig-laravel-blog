<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command {
    protected $signature = 'blog:backup';
    protected $description = 'Generate a daily database snapshot archive';

    public function handle(): int {
        $this->info('Generating automated database archive...');
        $backup = BackupService::createDatabaseBackup();
        $this->info("Backup archive {$backup->filename} created successfully ({$backup->formatted_size}).");
        return self::SUCCESS;
    }
}
