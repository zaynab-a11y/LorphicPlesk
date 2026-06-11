<?php

namespace App\Console\Commands;

use App\Models\GeneratedBlog;
use App\Services\BlogGeneratorService;
use Illuminate\Console\Command;

class GenerateBlog extends Command
{
    protected $signature = 'blog:generate {id}';
    protected $description = 'Generate blog content for a pending GeneratedBlog record';

    public function handle(BlogGeneratorService $svc): void
    {
        $blog = GeneratedBlog::findOrFail($this->argument('id'));

        if ($blog->status !== 'pending') {
            $this->info('Blog is not pending, skipping.');
            return;
        }

        try {
            $homepageText = $svc->fetchHomepageText($blog->domain);
            $businessType = $homepageText !== "Business website: {$blog->domain}"
                ? substr($homepageText, 0, 200)
                : $blog->domain;

            $content = $svc->generate(
                $blog->domain,
                $businessType,
                'United States',
                $blog->topic,
                $blog->focus_keyword,
                $blog->word_count_target ?? 2000,
                $homepageText
            );

            $blog->update([
                'content'           => $content ?: '',
                'word_count_actual' => $content ? str_word_count(strip_tags($content)) : 0,
                'status'            => $content ? 'done' : 'failed',
            ]);

            $this->info($content ? 'Done.' : 'Failed — empty response from Claude.');
        } catch (\Throwable $e) {
            $blog->update(['status' => 'failed']);
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
