<x-app-layout>
    <x-slot name="title">Edit {{ $project->name }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Projects</a> /
        <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a> / Edit
    </x-slot>

    <div class="max-w-xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                    <input type="text" name="name" value="{{ old('name', $project->name) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                    <input type="text" name="domain" value="{{ old('domain', $project->domain) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site URL</label>
                    <input type="url" name="url" value="{{ old('url', $project->url) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach(['active', 'paused', 'archived'] as $s)
                            <option value="{{ $s }}" @selected(old('status', $project->status) === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('notes', $project->notes) }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        Save Changes
                    </button>
                    <a href="{{ route('projects.show', $project) }}"
                       class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-red-600 mb-2">Danger Zone</h3>
                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete this project and all its data? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-sm text-red-600 border border-red-300 hover:bg-red-50 px-4 py-2 rounded-lg transition">
                        Delete Project
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
