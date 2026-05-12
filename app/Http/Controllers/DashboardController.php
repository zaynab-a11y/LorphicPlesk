<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Keyword;
use App\Models\Backlink;
use App\Models\SeoIssue;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::where('user_id', auth()->id())
            ->with('latestAudit')
            ->withCount(['keywords', 'backlinks', 'seoIssues' => fn($q) => $q->where('is_resolved', false)])
            ->get();

        $totalKeywords  = $projects->sum('keywords_count');
        $totalBacklinks = $projects->sum('backlinks_count');
        $totalIssues    = $projects->sum('seo_issues_count');
        $avgScore       = $projects->filter(fn($p) => $p->latestAudit)
            ->avg(fn($p) => $p->latestAudit->seo_score) ?? 0;

        $topKeywords = Keyword::whereIn('project_id', $projects->pluck('id'))
            ->with(['latestRanking', 'project'])
            ->get()
            ->sortBy(fn($k) => $k->latestRanking?->position ?? 999)
            ->take(10);

        $recentIssues = SeoIssue::whereIn('project_id', $projects->pluck('id'))
            ->where('is_resolved', false)
            ->with(['project', 'page'])
            ->orderBy('severity')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'projects', 'totalKeywords', 'totalBacklinks',
            'totalIssues', 'avgScore', 'topKeywords', 'recentIssues'
        ));
    }
}
