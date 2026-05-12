<x-app-layout :project="$project">
    <x-slot name="title">Page Details</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.pages.index', $project) }}" class="hover:text-indigo-600">Pages</a> / Details
    </x-slot>

    <div class="max-w-3xl space-y-5">
        {{-- Meta overview --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Page Overview</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['URL', $page->url],
                    ['HTTP Status', $page->http_status ?? '—'],
                    ['Title', $page->title ?? 'Missing'],
                    ['H1', $page->h1 ?? 'Missing'],
                    ['Meta Description', $page->meta_description ?? 'Missing'],
                    ['Word Count', $page->word_count ? number_format($page->word_count) : '—'],
                    ['Load Time', $page->load_time_ms ? number_format($page->load_time_ms).' ms' : '—'],
                    ['Desktop Score', $page->desktop_score ? (int)$page->desktop_score : '—'],
                    ['Mobile Score', $page->mobile_score ? (int)$page->mobile_score : '—'],
                    ['Indexed', $page->is_indexed ? 'Yes' : 'No'],
                    ['Has Canonical', $page->has_canonical ? 'Yes' : 'No'],
                    ['Last Crawled', $page->last_crawled_at?->diffForHumans() ?? '—'],
                ] as [$key, $val])
                    <div>
                        <dt class="text-xs font-medium text-gray-400">{{ $key }}</dt>
                        <dd class="text-sm text-gray-800 mt-0.5 break-all">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Issues on this page --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">SEO Issues on This Page</h2>
            @forelse($page->seoIssues as $issue)
                <div class="flex items-start gap-3 py-2.5 border-b border-gray-50 last:border-0">
                    <span class="mt-1 w-2 h-2 rounded-full flex-shrink-0
                        {{ $issue->severity === 'critical' ? 'bg-red-500' : ($issue->severity === 'warning' ? 'bg-yellow-400' : 'bg-blue-400') }}"></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ ucwords(str_replace('_', ' ', $issue->type)) }}</p>
                        @if($issue->description)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $issue->description }}</p>
                        @endif
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0
                        {{ $issue->severity === 'critical' ? 'bg-red-100 text-red-700' :
                           ($issue->severity === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $issue->severity }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center">No issues on this page.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
