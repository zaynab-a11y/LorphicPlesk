<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Backlink;
use Illuminate\Http\Request;

class BacklinkController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $backlinks = $project->backlinks()
            ->orderByDesc('domain_authority')
            ->paginate(25);

        $stats = [
            'total'     => $project->backlinks()->count(),
            'active'    => $project->backlinks()->where('is_active', true)->count(),
            'dofollow'  => $project->backlinks()->where('link_type', 'dofollow')->count(),
            'avg_da'    => $project->backlinks()->avg('domain_authority') ?? 0,
        ];

        return view('backlinks.index', compact('project', 'backlinks', 'stats'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'source_url'       => 'required|url|max:500',
            'target_url'       => 'required|url|max:500',
            'anchor_text'      => 'nullable|string|max:255',
            'link_type'        => 'required|in:dofollow,nofollow,ugc,sponsored',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'page_authority'   => 'nullable|integer|min:0|max:100',
            'spam_score'       => 'nullable|integer|min:0|max:100',
        ]);

        $project->backlinks()->create([
            ...$validated,
            'first_seen_at'   => now(),
            'last_checked_at' => now(),
        ]);

        return back()->with('success', 'Backlink added.');
    }

    public function destroy(Project $project, Backlink $backlink)
    {
        $this->authorize('update', $project);
        $backlink->delete();

        return back()->with('success', 'Backlink removed.');
    }
}
