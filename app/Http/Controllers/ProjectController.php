<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('user_id', auth()->id())
            ->with('latestAudit')
            ->withCount(['keywords', 'backlinks', 'pages',
                'seoIssues' => fn($q) => $q->where('is_resolved', false)])
            ->latest()
            ->paginate(12);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'url'    => 'required|url|max:500',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $project = Project::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load('latestAudit');
        $project->loadCount(['keywords', 'backlinks', 'pages',
            'seoIssues' => fn($q) => $q->where('is_resolved', false)]);

        $rankingHistory = $project->keywords()
            ->with(['rankings' => fn($q) => $q->orderBy('checked_at')->take(30)])
            ->get();

        $issuesByType = $project->seoIssues()
            ->where('is_resolved', false)
            ->selectRaw('severity, count(*) as count')
            ->groupBy('severity')
            ->pluck('count', 'severity');

        $recentIssues = $project->seoIssues()
            ->where('is_resolved', false)
            ->with('page')
            ->orderByRaw("FIELD(severity,'critical','warning','notice')")
            ->latest()
            ->take(10)
            ->get();

        $auditHistory = $project->siteAudits()
            ->orderBy('created_at')
            ->take(30)
            ->get();

        return view('projects.show', compact(
            'project', 'rankingHistory', 'issuesByType', 'recentIssues', 'auditHistory'
        ));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'url'    => 'required|url|max:500',
            'status' => 'required|in:active,paused,archived',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted.');
    }
}
