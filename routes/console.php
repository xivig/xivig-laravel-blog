<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('blog:publish-scheduled')->everyMinute();
Schedule::command('sitemap:generate')->daily();
Schedule::command('blog:seo-audit')->daily();
Schedule::command('blog:backup')->daily();
Schedule::command('blog:clear-logs --days=30')->weekly();
