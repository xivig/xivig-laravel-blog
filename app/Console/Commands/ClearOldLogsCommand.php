<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\NotFoundLog;
use App\Models\PageView;
use Illuminate\Console\Command;

class ClearOldLogsCommand extends Command {
    protected $signature = 'blog:clear-logs {--days=60 : Days to retain logs}';
    protected $description = 'Purge activity, 404, and visitor telemetry logs older than specified days';

    public function handle(): int {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $nf = NotFoundLog::where('created_at', '<', $cutoff)->delete();
        $pv = PageView::where('created_at', '<', $cutoff)->delete();
        $al = ActivityLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Purged {$nf} 404 logs, {$pv} page views, and {$al} activity logs older than {$days} days.");
        return self::SUCCESS;
    }
}
