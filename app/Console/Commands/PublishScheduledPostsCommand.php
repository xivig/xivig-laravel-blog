<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPostsCommand extends Command {
    protected $signature = 'blog:publish-scheduled';
    protected $description = 'Publish posts whose scheduled publication date has arrived';

    public function handle(): int {
        $posts = Post::where('status', 'scheduled')
            ->where('published_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts to publish at this time.');
            return Command::SUCCESS;
        }

        foreach ($posts as $post) {
            $post->update(['status' => 'published']);
            $this->info("Published: {$post->title} (ID: {$post->id})");
        }

        $this->info("Published {$posts->count()} scheduled post(s).");
        return Command::SUCCESS;
    }
}
