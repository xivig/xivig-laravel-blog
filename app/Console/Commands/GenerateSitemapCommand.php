<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemapCommand extends Command {
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the XML sitemap for SEO and search engine indexing';

    public function handle(): int {
        $this->info('Generating XML sitemap...');

        $xml = SitemapService::generateXml();
        $publicPath = public_path('sitemap.xml');

        File::put($publicPath, $xml);

        $this->info("XML sitemap generated successfully at: {$publicPath}");
        return Command::SUCCESS;
    }
}
