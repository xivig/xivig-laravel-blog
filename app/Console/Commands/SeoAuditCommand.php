<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\SeoService;
use Illuminate\Console\Command;

class SeoAuditCommand extends Command {
    protected $signature = 'blog:seo-audit';
    protected $description = 'Perform an automated SEO audit across all published chronicles';

    public function handle(): int {
        $posts = Post::published()->with(['author', 'category'])->get();

        if ($posts->isEmpty()) {
            $this->warn('No published posts found to audit.');
            return Command::SUCCESS;
        }

        $rows = [];
        $totalScore = 0;

        foreach ($posts as $post) {
            $audit = SeoService::auditPost($post);
            $totalScore += $audit['score'];
            $rows[] = [
                $post->id,
                $post->title,
                $audit['score'] . '/100 (' . $audit['grade'] . ')',
                $audit['word_count'] . ' words',
                $post->meta_description ? 'Configured' : 'Fallback Excerpt',
            ];
        }

        $avgScore = round($totalScore / $posts->count(), 1);

        $this->info("=== MAISON GAZETTE SEO HEALTH AUDIT ===");
        $this->table(['ID', 'Title', 'SEO Score', 'Word Count', 'Meta Description'], $rows);
        $this->info("Average Editorial SEO Health: {$avgScore}/100");

        return Command::SUCCESS;
    }
}
