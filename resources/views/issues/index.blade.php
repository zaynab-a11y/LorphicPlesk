<x-app-layout :project="$project">
    <x-slot name="title">SEO Issues — {{ $project->name }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Projects</a> /
        <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a> / Issues
    </x-slot>

    {{-- Severity tabs --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('projects.issues.index', $project) }}"
           class="text-sm font-medium px-4 py-1.5 rounded-full border transition
               {{ !request()->filled('severity') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-500' }}">
            All <span class="ml-1 opacity-70">{{ array_sum($counts) }}</span>
        </a>
        @foreach(['critical' => 'red', 'warning' => 'yellow', 'notice' => 'blue'] as $sev => $color)
            <a href="{{ route('projects.issues.index', $project) }}?severity={{ $sev }}"
               class="text-sm font-medium px-4 py-1.5 rounded-full border transition
                   {{ request('severity') === $sev ? "bg-{$color}-600 text-white border-{$color}-600" : 'bg-white text-gray-600 border-gray-300 hover:border-gray-500' }}">
                {{ ucfirst($sev) }} <span class="ml-1 opacity-70">{{ $counts[$sev] }}</span>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('projects.issues.bulk-resolve', $project) }}">
            @csrf
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                <label class="flex items-center gap-2 text-xs font-medium text-gray-600">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600">
                    Select all
                </label>
                <button type="submit"
                        class="text-xs bg-green-600 hover:bg-green-700 text-white font-medium px-3 py-1.5 rounded-lg transition">
                    Resolve Selected
                </button>
            </div>

            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-10 px-4 py-3"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Issue</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Page</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Severity</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($issues as $issue)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="issue_ids[]" value="{{ $issue->id }}"
                                       class="issue-checkbox rounded border-gray-300 text-indigo-600">
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">
                                    {{ ucwords(str_replace('_', ' ', $issue->type)) }}
                                </p>
                                @if($issue->description)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($issue->description, 80) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                @if($issue->page)
                                    <a href="{{ route('projects.pages.show', [$project, $issue->page]) }}"
                                       class="text-xs text-indigo-600 hover:text-indigo-800 truncate block">
                                        {{ $issue->page->url }}
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">Site-wide</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                    {{ $issue->severity === 'critical' ? 'bg-red-100 text-red-700' :
                                       ($issue->severity === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $issue->severity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('projects.issues.resolve', [$project, $issue]) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-medium">
                                        Resolve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                                {{ request()->filled('severity') ? 'No '.request('severity').' issues.' : 'No open issues!' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($issues->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $issues->links() }}</div>
            @endif
        </form>
    </div>
</x-app-layout>

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.issue-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
