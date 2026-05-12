<x-app-layout>
    <x-slot name="title">New Project</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-600">Projects</a> / New
    </x-slot>

    <div class="max-w-xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. My Blog"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                    <input type="text" name="domain" value="{{ old('domain') }}"
                           placeholder="example.com"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('domain') border-red-400 @enderror">
                    @error('domain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site URL</label>
                    <input type="url" name="url" value="{{ old('url') }}"
                           placeholder="https://example.com"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('url') border-red-400 @enderror">
                    @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        Create Project
                    </button>
                    <a href="{{ route('projects.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
