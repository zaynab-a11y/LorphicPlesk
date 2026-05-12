<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Keyword;
use App\Models\KeywordRanking;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $keywords = $project->keywords()
            ->with('latestRanking')
            ->paginate(25);

        return view('keywords.index', compact('project', 'keywords'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'keywords'      => 'required|string',
            'target_url'    => 'nullable|url|max:500',
            'search_engine' => 'required|in:google,bing,yahoo',
            'location'      => 'required|string|max:100',
            'device'        => 'required|in:desktop,mobile,tablet',
        ]);

        $lines = array_filter(array_map('trim', explode("\n", $validated['keywords'])));

        $created = 0;
        foreach ($lines as $kw) {
            Keyword::firstOrCreate(
                [
                    'project_id'    => $project->id,
                    'keyword'       => $kw,
                    'search_engine' => $validated['search_engine'],
                    'location'      => $validated['location'],
                    'device'        => $validated['device'],
                ],
                ['target_url' => $validated['target_url']]
            );
            $created++;
        }

        return back()->with('success', "$created keyword(s) added.");
    }

    public function destroy(Project $project, Keyword $keyword)
    {
        $this->authorize('update', $project);
        $keyword->delete();

        return back()->with('success', 'Keyword removed.');
    }

    public function rankingHistory(Project $project, Keyword $keyword)
    {
        $this->authorize('view', $project);

        $rankings = $keyword->rankings()
            ->orderBy('checked_at')
            ->get()
            ->map(fn($r) => [
                'date'     => $r->checked_at->format('Y-m-d'),
                'position' => $r->position,
            ]);

        return response()->json($rankings);
    }
}
