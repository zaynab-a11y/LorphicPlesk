<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Project $project, Request $request)
    {
        $this->authorize('view', $project);

        $query = $project->pages()->with('seoIssues');

        if ($request->filled('status')) {
            $query->where('http_status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('url', 'like', '%' . $request->search . '%');
        }

        $pages = $query->orderByDesc('last_crawled_at')->paginate(25)->withQueryString();

        $statusCounts = $project->pages()
            ->selectRaw('CASE
                WHEN http_status >= 500 THEN "5xx"
                WHEN http_status >= 400 THEN "4xx"
                WHEN http_status >= 300 THEN "3xx"
                ELSE "2xx" END as status_group,
                count(*) as count')
            ->groupBy('status_group')
            ->pluck('count', 'status_group');

        return view('pages.index', compact('project', 'pages', 'statusCounts'));
    }

    public function show(Project $project, Page $page)
    {
        $this->authorize('view', $project);
        $page->load(['seoIssues' => fn($q) => $q->where('is_resolved', false)]);

        return view('pages.show', compact('project', 'page'));
    }
}
