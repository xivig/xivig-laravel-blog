<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ZipArchive;

class CleanDeploymentCommand extends Command {
    protected $signature = 'deploy:clean
                            {--backups : Purge accumulated development database snapshots in storage/app/backups}
                            {--logs : Truncate and purge development log files}
                            {--sessions : Purge active session state files}
                            {--cache : Purge views, routes, config, and framework caches}
                            {--all : Execute complete cleanup of backups, logs, sessions, and caches}
                            {--check : Run a comprehensive security audit of deployment readiness}
                            {--package : Generate a clean, pristine release archive (.zip) without sensitive files}';

    protected $description = 'Clean development databases, backups, logs, sessions, and create pristine deployment packages';

    public function handle(): int {
        $this->newLine();
        $this->line('<fg=yellow;options=bold>===============================================================</>');
        $this->line('<fg=yellow;options=bold>   MAISON GAZETTE &bull; DEPLOYMENT CLEANUP & AUDIT SUITE      </>');
        $this->line('<fg=yellow;options=bold>===============================================================</>');
        $this->newLine();

        if ($this->option('check')) {
            return $this->runDeploymentAudit();
        }

        if ($this->option('package')) {
            return $this->createCleanPackage();
        }

        $all = $this->option('all') || (!$this->option('backups') && !$this->option('logs') && !$this->option('sessions') && !$this->option('cache'));

        // 1. Clean Backups
        if ($all || $this->option('backups')) {
            $this->cleanBackups();
        }

        // 2. Clean Logs
        if ($all || $this->option('logs')) {
            $this->cleanLogs();
        }

        // 3. Clean Sessions
        if ($all || $this->option('sessions')) {
            $this->cleanSessions();
        }

        // 4. Clean Cache
        if ($all || $this->option('cache')) {
            $this->cleanCaches();
        }

        $this->newLine();
        $this->info('Deployment hygiene cleanup completed successfully.');
        $this->newLine();
        return self::SUCCESS;
    }

    protected function cleanBackups(): void {
        $backupDir = storage_path('app/backups');
        $count = 0;
        $freedBytes = 0;

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                if ($file->getFilename() === '.gitignore' || $file->getFilename() === '.gitkeep') {
                    continue;
                }
                $freedBytes += $file->getSize();
                File::delete($file->getPathname());
                $count++;
            }
        }

        // Clear backup records in DB
        try {
            Backup::truncate();
        } catch (\Throwable $e) {}

        $freedMb = round($freedBytes / 1024 / 1024, 2);
        $this->info("Purged {$count} development database backup snapshots ({$freedMb} MB freed).");
    }

    protected function cleanLogs(): void {
        $logDir = storage_path('logs');
        $count = 0;

        if (File::exists($logDir)) {
            $files = File::files($logDir);
            foreach ($files as $file) {
                if ($file->getExtension() === 'log') {
                    File::put($file->getPathname(), '');
                    $count++;
                }
            }
        }

        $this->info("Truncated {$count} application log files in storage/logs.");
    }

    protected function cleanSessions(): void {
        $sessionsDir = storage_path('framework/sessions');
        $count = 0;

        if (File::exists($sessionsDir)) {
            $files = File::files($sessionsDir);
            foreach ($files as $file) {
                if ($file->getFilename() === '.gitignore' || $file->getFilename() === '.gitkeep') {
                    continue;
                }
                File::delete($file->getPathname());
                $count++;
            }
        }

        $this->info("Purged {$count} session state files in storage/framework/sessions.");
    }

    protected function cleanCaches(): void {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        // Also clean views folder
        $viewsDir = storage_path('framework/views');
        if (File::exists($viewsDir)) {
            foreach (File::files($viewsDir) as $file) {
                if ($file->getFilename() !== '.gitignore' && $file->getFilename() !== '.gitkeep') {
                    File::delete($file->getPathname());
                }
            }
        }

        $this->info('Flushed application, view, config, and routing caches.');
    }

    protected function runDeploymentAudit(): int {
        $this->info('Evaluating Production Deployment Security Readiness:');
        $this->newLine();

        $rows = [];

        // 1. App Environment & Debug
        $env = config('app.env');
        $debug = config('app.debug');
        $rows[] = [
            'Environment (APP_ENV)',
            $env,
            $env === 'production' ? '<fg=green>PASS</>' : '<fg=yellow>WARNING (Not production)</>',
        ];
        $rows[] = [
            'Debug Mode (APP_DEBUG)',
            $debug ? 'true' : 'false',
            !$debug ? '<fg=green>PASS</>' : '<fg=red>FAIL (Must be false in production)</>',
        ];

        // 2. Default Password Check
        $admin = User::where('email', 'admin@xivig.com')->first();
        if ($admin && \Illuminate\Support\Facades\Hash::check('admin123456', $admin->password)) {
            $rows[] = [
                'Admin Credentials',
                'admin@xivig.com (Default password active)',
                '<fg=red;options=bold>FAIL - Default credentials retain admin123456</>',
            ];
        } else {
            $rows[] = [
                'Admin Credentials',
                $admin ? 'Secured / Custom' : 'Custom email',
                '<fg=green>PASS (No default password)</>',
            ];
        }

        // 3. Database Location
        $dbConn = config('database.default');
        $dbPath = config('database.connections.sqlite.database');
        $isPublic = str_contains(str_replace('\\', '/', (string)$dbPath), '/public/');
        $rows[] = [
            'Database Location',
            $dbConn . ' (' . ($isPublic ? 'Publicly exposed!' : 'Private filesystem') . ')',
            !$isPublic ? '<fg=green>PASS</>' : '<fg=red>FAIL (Database in public directory)</>',
        ];

        // 4. Backups Clutter
        $backupFiles = File::exists(storage_path('app/backups'))
            ? count(array_filter(File::files(storage_path('app/backups')), fn($f) => !in_array($f->getFilename(), ['.gitignore', '.gitkeep'])))
            : 0;
        $rows[] = [
            'Development Backups',
            "{$backupFiles} backup file(s) found",
            $backupFiles === 0 ? '<fg=green>PASS (Clean)</>' : "<fg=yellow>NOTICE ({$backupFiles} files to clean)</>",
        ];

        // 5. Active Sessions
        $sessionFiles = File::exists(storage_path('framework/sessions'))
            ? count(array_filter(File::files(storage_path('framework/sessions')), fn($f) => !in_array($f->getFilename(), ['.gitignore', '.gitkeep'])))
            : 0;
        $rows[] = [
            'Session State Clutter',
            "{$sessionFiles} session file(s)",
            $sessionFiles === 0 ? '<fg=green>PASS (Clean)</>' : "<fg=yellow>NOTICE ({$sessionFiles} active sessions)</>",
        ];

        // 6. Logs Hygiene
        $logSize = File::exists(storage_path('logs/laravel.log')) ? File::size(storage_path('logs/laravel.log')) : 0;
        $logMb = round($logSize / 1024 / 1024, 2);
        $rows[] = [
            'Application Logs',
            "{$logMb} MB in laravel.log",
            $logMb < 1 ? '<fg=green>PASS</>' : "<fg=yellow>NOTICE ({$logMb} MB dev logs to truncate)</>",
        ];

        // 7. .gitignore and .gitattributes Presence
        $hasGitignore = File::exists(base_path('.gitignore'));
        $rows[] = [
            '.gitignore Configuration',
            $hasGitignore ? 'Configured' : 'Missing',
            $hasGitignore ? '<fg=green>PASS</>' : '<fg=red>FAIL (Missing .gitignore)</>',
        ];

        $this->table(['Security & Hygiene Metric', 'Current Value', 'Status'], $rows);
        $this->newLine();

        $this->comment('Recommendation: Run `php artisan deploy:clean --all` before deploying or archiving.');
        return self::SUCCESS;
    }

    protected function createCleanPackage(): int {
        if (!class_exists('ZipArchive')) {
            $this->error('PHP ZipArchive extension is required to generate release archive.');
            return self::FAILURE;
        }

        $this->info('Building pristine distribution package (excluding dev databases, backups, sessions, and logs)...');

        $distDir = base_path('dist');
        if (!File::exists($distDir)) {
            File::makeDirectory($distDir, 0755, true);
        }

        $zipFile = $distDir . '/xivig-gazette-clean-release-' . date('Ymd_His') . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Cannot create zip file at: {$zipFile}");
            return self::FAILURE;
        }

        $basePath = realpath(base_path());

        // Excluded relative patterns
        $excludePrefixes = [
            'database' . DIRECTORY_SEPARATOR . 'database.sqlite',
            'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR,
            'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR,
            'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions' . DIRECTORY_SEPARATOR,
            'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
            'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR,
            'bootstrap' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
            '.git' . DIRECTORY_SEPARATOR,
            '.env',
            '.env.backup',
            '.phpunit.result.cache',
            'dist' . DIRECTORY_SEPARATOR,
            'node_modules' . DIRECTORY_SEPARATOR,
        ];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $addedCount = 0;
        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($basePath) + 1);

            // Check exclusions
            $skip = false;
            foreach ($excludePrefixes as $prefix) {
                if (str_starts_with($relativePath, $prefix) || $relativePath === $prefix) {
                    // Allow .gitkeep and .gitignore in storage folders
                    if ($file->getFilename() === '.gitkeep' || $file->getFilename() === '.gitignore') {
                        $skip = false;
                        break;
                    }
                    $skip = true;
                    break;
                }
            }

            if ($skip) {
                continue;
            }

            $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
            $addedCount++;
        }

        $zip->close();
        $sizeMb = round(File::size($zipFile) / 1024 / 1024, 2);

        $this->info("Clean release archive created successfully: {$zipFile} ({$sizeMb} MB, {$addedCount} files packed).");
        $this->comment('This archive contains ZERO development databases, backups, active sessions, or logs.');
        return self::SUCCESS;
    }
}
