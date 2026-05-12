<x-app-layout :project="$project">
    <x-slot name="title">Keywords — {{ $project->name }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Projects</a> /
        <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a> / Keywords
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Add keywords form --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Add Keywords</h2>
                <form method="POST" action="{{ route('projects.keywords.store', $project) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Keywords <span class="text-gray-400">(one per line)</span>
                        </label>
                        <textarea name="keywords" rows="6" placeholder="seo tools&#10;best seo software&#10;rank tracker"
                                  class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('keywords') border-red-400 @enderror"></textarea>
                        @error('keywords') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Search Engine</label>
                            <select name="search_engine" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="google">Google</option>
                                <option value="bing">Bing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Device</label>
                            <select name="device" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="desktop">Desktop</option>
                                <option value="mobile">Mobile</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Location</label>
                        <input type="text" name="location" value="US"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Target URL <span class="text-gray-400">(optional)</span></label>
                        <input type="url" name="target_url" placeholder="https://..."
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        Add Keywords
                    </button>
                </form>
            </div>
        </div>

        {{-- Keywords table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keyword</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Volume</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($keywords as $keyword)
                            @php $r = $keyword->latestRanking @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $keyword->keyword }}</p>
                                    <p class="text-xs text-gray-400">{{ $keyword->search_engine }} · {{ $keyword->location }} · {{ $keyword->device }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($r && $r->position)
                                        <span class="inline-flex items-center justify-center w-10 h-7 rounded font-bold text-xs
                                            {{ $r->position <= 3 ? 'bg-green-100 text-green-700' :
                                               ($r->position <= 10 ? 'bg-blue-100 text-blue-700' :
                                               ($r->position <= 30 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600')) }}">
                                            #{{ $r->position }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $keyword->search_volume ? number_format($keyword->search_volume) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('projects.keywords.destroy', [$project, $keyword]) }}"
                                          onsubmit="return confirm('Remove this keyword?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-400">
                                    No keywords tracked yet. Add some on the left.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($keywords->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $keywords->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
