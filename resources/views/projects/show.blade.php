<x-app-layout>
    <x-slot name="title">{{ $project->name }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Projects</a> / {{ $project->name }}
    </x-slot>
    <x-slot name="actions">
        <a href="{{ route('projects.edit', $project) }}"
           class="inline-flex items-center gap-1 text-sm text-gray-600 border border-gray-300 hover:border-gray-400 px-3 py-1.5 rounded-lg transition">
            Edit
        </a>
    </x-slot>

    @php $audit = $project->latestAudit @endphp

    {{-- Score & stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
        <div class="col-span-2 md:col-span-1 bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col items-center justify-center">
            @php $score = $audit ? (int)$audit->seo_score : 0 @endphp
            <div class="w-20 h-20 rounded-full border-8 flex items-center justify-center font-bold text-2xl
                {{ $score >= 80 ? 'border-green-400 text-green-700' : ($score >= 60 ? 'border-yellow-400 text-yellow-700' : 'border-red-400 text-red-700') }}">
                {{ $score }}
            </div>
            <p class="text-xs text-gray-400 mt-2">SEO Score</p>
        </div>

        @foreach([
            ['Keywords', $project->keywords_count, 'text-blue-600', route('projects.keywords.index', $project)],
            ['Backlinks', $project->backlinks_count, 'text-green-600', route('projects.backlinks.index', $project)],
            ['Pages', $project->pages_count, 'text-purple-600', route('projects.pages.index', $project)],
            ['Open Issues', $project->seo_issues_count, $project->seo_issues_count > 0 ? 'text-red-600' : 'text-gray-700', route('projects.issues.index', $project)],
        ] as [$label, $count, $color, $link])
            <a href="{{ $link }}"
               class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center hover:border-indigo-300 transition">
                <p class="text-2xl font-bold {{ $color }}">{{ number_format($count) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Score history chart --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">SEO Score History</h2>
            <canvas id="scoreChart" height="120"></canvas>
        </div>

        {{-- Issues by severity --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Open Issues</h2>
                <a href="{{ route('projects.issues.index', $project) }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800">View all →</a>
            </div>

            @if($issuesByType->sum() > 0)
                <div class="space-y-3">
                    @foreach(['critical' => 'red', 'warning' => 'yellow', 'notice' => 'blue'] as $sev => $color)
                        @php $cnt = $issuesByType->get($sev, 0) @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-16 text-xs font-medium text-gray-600">{{ ucfirst($sev) }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full bg-{{ $color }}-400"
                                     style="width: {{ $issuesByType->sum() > 0 ? ($cnt / $issuesByType->sum() * 100) : 0 }}%"></div>
                            </div>
                            <span class="w-8 text-xs text-right font-semibold text-gray-700">{{ $cnt }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 py-8 text-center">No open issues!</p>
            @endif

            <div class="mt-5 space-y-2">
                @foreach($recentIssues as $issue)
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full flex-shrink-0
                            {{ $issue->severity === 'critical' ? 'bg-red-500' : ($issue->severity === 'warning' ? 'bg-yellow-400' : 'bg-blue-400') }}"></span>
                        <span class="text-gray-700 font-medium">{{ ucwords(str_replace('_', ' ', $issue->type)) }}</span>
                        <span class="text-gray-400 truncate">{{ $issue->page?->url }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Audit history --}}
        @if($audit)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Latest Audit Summary</h2>
            <dl class="grid grid-cols-2 gap-3">
                @foreach([
                    ['Pages Crawled', $audit->pages_crawled],
                    ['Broken Links', $audit->broken_links],
                    ['Pages w/ Errors', $audit->pages_with_errors],
                    ['Avg Load Time', $audit->avg_load_time ? number_format($audit->avg_load_time, 1).'s' : '—'],
                ] as [$key, $val])
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-xs text-gray-400">{{ $key }}</dt>
                        <dd class="text-base font-semibold text-gray-800 mt-0.5">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>
            <p class="text-xs text-gray-400 mt-3">Last audit: {{ $audit->created_at->diffForHumans() }}</p>
        </div>
        @endif

        {{-- Quick nav --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Quick Navigation</h2>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    ['Keywords', route('projects.keywords.index', $project), 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
                    ['Backlinks', route('projects.backlinks.index', $project), 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
                    ['Pages', route('projects.pages.index', $project), 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['Issues', route('projects.issues.index', $project), 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                ] as [$label, $href, $icon])
                    <a href="{{ $href }}"
                       class="flex items-center gap-2 p-3 rounded-lg border border-gray-100 hover:border-indigo-300 hover:bg-indigo-50 text-sm font-medium text-gray-700 hover:text-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
const auditData = @json($auditHistory->map(fn($a) => ['date' => $a->created_at->format('M j'), 'score' => $a->seo_score]));
if (auditData.length > 0) {
    new Chart(document.getElementById('scoreChart'), {
        type: 'line',
        data: {
            labels: auditData.map(d => d.date),
            datasets: [{
                label: 'SEO Score',
                data: auditData.map(d => d.score),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { min: 0, max: 100, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } }
            }
        }
    });
}
</script>
@endpush
