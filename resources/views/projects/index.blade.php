<x-app-layout>
    <x-slot name="title">Projects</x-slot>
    <x-slot name="actions">
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Project
        </a>
    </x-slot>

    @if($projects->isEmpty())
        <div class="max-w-md mx-auto mt-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-700">No projects yet</h3>
            <p class="text-gray-400 mt-1 text-sm">Track SEO performance for your websites.</p>
            <a href="{{ route('projects.create') }}"
               class="mt-4 inline-block bg-indigo-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition">
                Create your first project
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($projects as $project)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <a href="{{ route('projects.show', $project) }}"
                               class="font-semibold text-gray-900 hover:text-indigo-600 truncate block">
                                {{ $project->name }}
                            </a>
                            <a href="{{ $project->url }}" target="_blank"
                               class="text-xs text-gray-400 hover:text-indigo-500 truncate block mt-0.5">
                                {{ $project->domain }}
                            </a>
                        </div>
                        @if($project->latestAudit)
                            @php $score = (int)$project->latestAudit->seo_score @endphp
                            <div class="ml-3 flex-shrink-0 w-12 h-12 rounded-full border-4 flex items-center justify-center font-bold text-sm
                                {{ $score >= 80 ? 'border-green-400 text-green-700' : ($score >= 60 ? 'border-yellow-400 text-yellow-700' : 'border-red-400 text-red-700') }}">
                                {{ $score }}
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-gray-50 text-center">
                        <div>
                            <p class="text-lg font-bold text-gray-800">{{ $project->keywords_count }}</p>
                            <p class="text-xs text-gray-400">Keywords</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-800">{{ $project->backlinks_count }}</p>
                            <p class="text-xs text-gray-400">Backlinks</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold {{ $project->seo_issues_count > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $project->seo_issues_count }}
                            </p>
                            <p class="text-xs text-gray-400">Issues</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $project->status === 'active' ? 'bg-green-100 text-green-700' :
                               ($project->status === 'paused' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($project->status) }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('projects.edit', $project) }}"
                               class="text-xs text-gray-400 hover:text-indigo-600">Edit</a>
                            <a href="{{ route('projects.show', $project) }}"
                               class="text-xs text-indigo-600 font-medium hover:text-indigo-800">View →</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif
</x-app-layout>
