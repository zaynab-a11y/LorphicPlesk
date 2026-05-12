<x-app-layout :project="$project">
    <x-slot name="title">Backlinks — {{ $project->name }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Projects</a> /
        <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a> / Backlinks
    </x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['Total', $stats['total'], 'text-gray-800'],
            ['Active', $stats['active'], 'text-green-700'],
            ['Dofollow', $stats['dofollow'], 'text-blue-700'],
            ['Avg DA', number_format($stats['avg_da'], 1), 'text-indigo-700'],
        ] as [$label, $value, $color])
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold {{ $color }}">{{ $value }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Add backlink form --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Add Backlink</h2>
                <form method="POST" action="{{ route('projects.backlinks.store', $project) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Source URL</label>
                        <input type="url" name="source_url" placeholder="https://referring-site.com/page"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('source_url') border-red-400 @enderror">
                        @error('source_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Target URL</label>
                        <input type="url" name="target_url" placeholder="{{ $project->url }}"
                               value="{{ $project->url }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Anchor Text</label>
                        <input type="text" name="anchor_text" placeholder="click here"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                            <select name="link_type" class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="dofollow">Dofollow</option>
                                <option value="nofollow">Nofollow</option>
                                <option value="ugc">UGC</option>
                                <option value="sponsored">Sponsored</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Domain Auth.</label>
                            <input type="number" name="domain_authority" min="0" max="100"
                                   class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        Add Backlink
                    </button>
                </form>
            </div>
        </div>

        {{-- Backlinks table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Source</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">DA</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($backlinks as $bl)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 max-w-xs">
                                    <a href="{{ $bl->source_url }}" target="_blank"
                                       class="text-indigo-600 hover:text-indigo-800 truncate block text-xs">
                                        {{ $bl->source_domain }}
                                    </a>
                                    @if($bl->anchor_text)
                                        <p class="text-xs text-gray-400 truncate mt-0.5">"{{ $bl->anchor_text }}"</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($bl->domain_authority)
                                        <span class="font-semibold {{ $bl->domain_authority >= 50 ? 'text-green-600' : ($bl->domain_authority >= 25 ? 'text-yellow-600' : 'text-gray-500') }}">
                                            {{ $bl->domain_authority }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                        {{ $bl->link_type === 'dofollow' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $bl->link_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="w-2 h-2 rounded-full inline-block {{ $bl->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('projects.backlinks.destroy', [$project, $bl]) }}"
                                          onsubmit="return confirm('Remove backlink?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                                    No backlinks added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($backlinks->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">{{ $backlinks->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
