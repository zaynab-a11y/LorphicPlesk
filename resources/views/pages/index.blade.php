<x-app-layout :project="$project">
    <x-slot name="title">Pages — {{ $project->name }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Projects</a> /
        <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a> / Pages
    </x-slot>

    {{-- Status filter + search --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search URL…"
               class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 w-64">

        <div class="flex gap-2">
            @foreach(['2xx' => 'OK', '3xx' => 'Redirects', '4xx' => 'Errors', '5xx' => 'Server'] as $group => $label)
                <a href="{{ request()->fullUrlWithQuery(['status' => $group]) }}"
                   class="text-xs font-medium px-3 py-1.5 rounded-full border transition
                       {{ request('status') === $group ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">
                    {{ $label }}
                    <span class="ml-1 opacity-70">{{ $statusCounts->get($group, 0) }}</span>
                </a>
            @endforeach
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('projects.pages.index', $project) }}"
                   class="text-xs font-medium px-3 py-1.5 rounded-full border border-gray-300 text-gray-500 hover:text-gray-700">
                    Clear
                </a>
            @endif
        </div>
        <button type="submit"
                class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700 transition">
            Search
        </button>
    </form>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">URL</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Load</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Score</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Issues</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pages as $page)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 max-w-xs">
                            <p class="font-medium text-gray-800 truncate">{{ $page->title ?? 'No title' }}</p>
                            <a href="{{ $page->url }}" target="_blank"
                               class="text-xs text-indigo-500 hover:text-indigo-700 truncate block">{{ $page->url }}</a>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($page->http_status)
                                <span class="font-semibold
                                    {{ $page->http_status >= 500 ? 'text-red-600' :
                                       ($page->http_status >= 400 ? 'text-orange-600' :
                                       ($page->http_status >= 300 ? 'text-yellow-600' : 'text-green-600')) }}">
                                    {{ $page->http_status }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-600">
                            {{ $page->load_time_ms ? number_format($page->load_time_ms).'ms' : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($page->desktop_score)
                                <span class="text-xs font-semibold
                                    {{ $page->desktop_score >= 80 ? 'text-green-700' :
                                       ($page->desktop_score >= 60 ? 'text-yellow-700' : 'text-red-700') }}">
                                    {{ (int)$page->desktop_score }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php $issueCount = $page->seo_issues_count ?? $page->seoIssues->where('is_resolved', false)->count() @endphp
                            @if($issueCount > 0)
                                <span class="text-xs font-bold text-red-600">{{ $issueCount }}</span>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('projects.pages.show', [$project, $page]) }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Details →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                            No pages crawled yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($pages->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $pages->links() }}</div>
        @endif
    </div>
</x-app-layout>
