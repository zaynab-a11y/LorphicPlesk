<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Keyword;
use App\Models\KeywordRanking;
use App\Models\Backlink;
use App\Models\Page;
use App\Models\SeoIssue;
use App\Models\SiteAudit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@lorphic.com'],
            [
                'name'     => 'Lorphic Admin',
                'password' => Hash::make('password'),
            ]
        );

        $projects = [
            ['name' => 'Lorphic Main Site', 'domain' => 'lorphic.com', 'url' => 'https://lorphic.com'],
            ['name' => 'Blog',               'domain' => 'blog.lorphic.com', 'url' => 'https://blog.lorphic.com'],
        ];

        foreach ($projects as $pd) {
            $project = Project::firstOrCreate(
                ['user_id' => $user->id, 'domain' => $pd['domain']],
                array_merge($pd, ['user_id' => $user->id, 'status' => 'active'])
            );

            // Audit history (30 days)
            $score = rand(45, 65);
            for ($i = 30; $i >= 0; $i--) {
                $score = max(0, min(100, $score + rand(-3, 5)));
                SiteAudit::create([
                    'project_id'      => $project->id,
                    'seo_score'       => $score,
                    'pages_crawled'   => rand(80, 200),
                    'issues_critical' => rand(0, 5),
                    'issues_warning'  => rand(3, 20),
                    'issues_notice'   => rand(10, 50),
                    'pages_with_errors' => rand(0, 10),
                    'broken_links'    => rand(0, 8),
                    'avg_load_time'   => round(rand(8, 40) / 10, 1),
                    'completed_at'    => now()->subDays($i),
                    'created_at'      => now()->subDays($i),
                ]);
            }

            // Keywords
            $keywords = [
                ['seo dashboard', 1200, 8],
                ['rank tracker tool', 880, 14],
                ['keyword position checker', 2400, 22],
                ['site audit software', 590, 5],
                ['backlink monitor', 740, 31],
                ['google ranking tracker', 3100, 19],
            ];

            foreach ($keywords as [$kw, $vol, $pos]) {
                $keyword = Keyword::firstOrCreate(
                    ['project_id' => $project->id, 'keyword' => $kw, 'search_engine' => 'google', 'location' => 'US', 'device' => 'desktop'],
                    ['search_volume' => $vol]
                );

                for ($i = 30; $i >= 0; $i--) {
                    $pos = max(1, min(100, $pos + rand(-2, 2)));
                    KeywordRanking::create([
                        'keyword_id'         => $keyword->id,
                        'position'           => $pos,
                        'estimated_traffic'  => max(0, (int)(($vol * 0.3) / max(1, $pos))),
                        'checked_at'         => now()->subDays($i)->toDateString(),
                        'created_at'         => now()->subDays($i),
                    ]);
                }
            }

            // Pages
            $paths = ['/', '/about', '/pricing', '/blog', '/contact', '/features', '/docs'];
            foreach ($paths as $path) {
                $page = Page::firstOrCreate(
                    ['project_id' => $project->id, 'url' => $pd['url'].$path],
                    [
                        'title'          => ucfirst(ltrim($path, '/') ?: 'Home') . ' — ' . $pd['name'],
                        'meta_description'=> 'Page description for ' . ltrim($path, '/'),
                        'h1'             => ucfirst(ltrim($path, '/') ?: 'Welcome'),
                        'word_count'     => rand(200, 1800),
                        'http_status'    => 200,
                        'load_time_ms'   => rand(300, 2500),
                        'desktop_score'  => rand(55, 98),
                        'mobile_score'   => rand(45, 90),
                        'is_indexed'     => true,
                        'has_canonical'  => (bool)rand(0, 1),
                        'last_crawled_at'=> now()->subHours(rand(1, 72)),
                    ]
                );

                // Add some issues
                $issueTypes = [
                    ['missing_meta', 'warning', 'Meta description is missing'],
                    ['slow_page', 'warning', 'Page load time exceeds 2 seconds'],
                    ['missing_alt', 'notice', 'Images missing alt attributes'],
                ];
                foreach (array_slice($issueTypes, 0, rand(0, 2)) as [$type, $sev, $desc]) {
                    SeoIssue::firstOrCreate(
                        ['project_id' => $project->id, 'page_id' => $page->id, 'type' => $type],
                        ['severity' => $sev, 'description' => $desc]
                    );
                }
            }

            // Broken page
            Page::firstOrCreate(
                ['project_id' => $project->id, 'url' => $pd['url'].'/old-page'],
                [
                    'http_status' => 404,
                    'last_crawled_at' => now()->subDay(),
                ]
            );

            SeoIssue::firstOrCreate(
                ['project_id' => $project->id, 'type' => 'broken_link'],
                ['severity' => 'critical', 'description' => 'Broken internal link detected: /old-page']
            );

            // Backlinks
            $bls = [
                ['https://techcrunch.com/article/seo-tools-2024', 92, 'dofollow'],
                ['https://moz.com/blog/top-tools', 88, 'dofollow'],
                ['https://searchengineland.com/review', 84, 'dofollow'],
                ['https://semrush.com/blog/comparison', 90, 'nofollow'],
                ['https://reddit.com/r/SEO/comments/abc', 75, 'ugc'],
            ];
            foreach ($bls as [$src, $da, $type]) {
                Backlink::firstOrCreate(
                    ['project_id' => $project->id, 'source_url' => $src],
                    [
                        'target_url'       => $pd['url'],
                        'anchor_text'      => 'best seo tool',
                        'link_type'        => $type,
                        'domain_authority' => $da,
                        'page_authority'   => $da - rand(5, 15),
                        'spam_score'       => rand(1, 10),
                        'is_active'        => true,
                        'first_seen_at'    => now()->subDays(rand(30, 180))->toDateString(),
                        'last_checked_at'  => now()->toDateString(),
                    ]
                );
            }
        }
    }
}
