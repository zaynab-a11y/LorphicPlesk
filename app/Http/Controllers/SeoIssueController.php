<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SeoIssue;
use Illuminate\Http\Request;

class SeoIssueController extends Controller
{
    public function index(Project $project, Request $request)
    {
        $this->authorize('view', $project);

        $query = $project->seoIssues()->with('page');

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->boolean('resolved')) {
            $query->where('is_resolved', true);
        } else {
            $query->where('is_resolved', false);
        }

        $issues = $query
            ->orderByRaw("FIELD(severity,'critical','warning','notice')")
            ->paginate(25)
            ->withQueryString();

        $counts = [
            'critical' => $project->seoIssues()->where('severity', 'critical')->where('is_resolved', false)->count(),
            'warning'  => $project->seoIssues()->where('severity', 'warning')->where('is_resolved', false)->count(),
            'notice'   => $project->seoIssues()->where('severity', 'notice')->where('is_resolved', false)->count(),
        ];

        return view('issues.index', compact('project', 'issues', 'counts'));
    }

    public function resolve(Project $project, SeoIssue $issue)
    {
        $this->authorize('update', $project);

        $issue->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Issue marked as resolved.');
    }

    public function bulkResolve(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'issue_ids'   => 'required|array',
            'issue_ids.*' => 'integer|exists:seo_issues,id',
        ]);

        SeoIssue::whereIn('id', $validated['issue_ids'])
            ->where('project_id', $project->id)
            ->update(['is_resolved' => true, 'resolved_at' => now()]);

        return back()->with('success', 'Issues resolved.');
    }
}
