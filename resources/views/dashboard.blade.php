<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg SEO Score</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600">{{ number_format($avgScore, 1) }}</p>
            <p class="text-xs text-gray-400 mt-1">across {{ $projects->count() }} project(s)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Keywords Tracked</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($totalKeywords) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Backlinks</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($totalBacklinks) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Open Issues</p>
            <p class="mt-2 text-3xl font-bold {{ $totalIssues > 0 ? 'text-red-600' : 'text-gray-700' }}">
                {{ number_format($totalIssues) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Projects list --}}
        <div class="lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800">Your Projects</h2>
                <a href="{{ route('projects.create') }}"
                   class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add project
                </a>
            </div>

            @forelse($projects as $project)
                <a href="{{ route('projects.show', $project) }}"
                   class="block bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-3 hover:border-indigo-300 transition">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $project->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $project->domain }}</p>
                        </div>
                        @if($project->latestAudit)
                            @php $score = $project->latestAudit->seo_score @endphp
                            <div class="ml-3 flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $score >= 80 ? 'bg-green-100 text-green-700' : ($score >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ (int)$score }}
                            </div>
                        @else
                            <span class="text-xs text-gray-400">No audit</span>
                        @endif
                    </div>
                    <div class="flex gap-4 mt-3 text-xs text-gray-500">
                        <span>{{ $project->keywords_count }} keywords</span>
                        <span>{{ $project->backlinks_count }} backlinks</span>
                        <span class="{{ $project->seo_issues_count > 0 ? 'text-red-500 font-medium' : '' }}">
                            {{ $project->seo_issues_count }} issues
                        </span>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-8 text-center">
                    <p class="text-gray-500 text-sm">No projects yet.</p>
                    <a href="{{ route('projects.create') }}"
                       class="mt-3 inline-block text-sm text-indigo-600 font-medium hover:text-indigo-800">
                        Create your first project →
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Right column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Top Keywords --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-base font-semibold text-gray-800 mb-4">Top Keyword Rankings</h2>
                @forelse($topKeywords as $keyword)
                    @php $ranking = $keyword->latestRanking @endphp
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $keyword->keyword }}</p>
                            <p class="text-xs text-gray-400">{{ $keyword->project->name }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex items-center gap-3">
                            @if($ranking)
                                <span class="inline-flex items-center justify-center w-9 h-7 rounded text-xs font-bold
                                    {{ $ranking->position <= 3 ? 'bg-green-100 text-green-700' :
                                       ($ranking->position <= 10 ? 'bg-blue-100 text-blue-700' :
                                       ($ranking->position <= 30 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600')) }}">
                                    #{{ $ranking->position }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No keywords tracked yet.</p>
                @endforelse
            </div>

            {{-- Recent Issues --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-base font-semibold text-gray-800 mb-4">Recent SEO Issues</h2>
                @forelse($recentIssues as $issue)
                    <div class="flex items-start gap-3 py-2 border-b border-gray-50 last:border-0">
                        <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0
                            {{ $issue->severity === 'critical' ? 'bg-red-500' : ($issue->severity === 'warning' ? 'bg-yellow-400' : 'bg-blue-400') }}"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-800">
                                {{ ucwords(str_replace('_', ' ', $issue->type)) }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ $issue->page?->url ?? $issue->project->domain }} · {{ $issue->project->name }}
                            </p>
                        </div>
                        <span class="ml-2 flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $issue->severity === 'critical' ? 'bg-red-100 text-red-700' :
                               ($issue->severity === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $issue->severity }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No open issues. Great job!</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
