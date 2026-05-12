<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Lorphic SEO') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="hidden md:flex md:flex-col w-64 bg-gray-900 text-white flex-shrink-0">
        <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-700">
            <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-lg font-semibold tracking-tight">Lorphic SEO</span>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('projects.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('projects.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Projects
            </a>

            @if(isset($project))
            <div class="pt-3 mt-3 border-t border-gray-700">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    {{ Str::limit($project->name, 20) }}
                </p>
                <a href="{{ route('projects.show', $project) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('projects.show') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Overview
                </a>
                <a href="{{ route('projects.keywords.index', $project) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('projects.keywords.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    Keywords
                </a>
                <a href="{{ route('projects.backlinks.index', $project) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('projects.backlinks.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Backlinks
                </a>
                <a href="{{ route('projects.pages.index', $project) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('projects.pages.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Pages
                </a>
                <a href="{{ route('projects.issues.index', $project) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('projects.issues.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    SEO Issues
                </a>
            </div>
            @endif
        </nav>

        <div class="border-t border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white" title="Logout">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 flex items-center justify-between px-6 py-4">
            <div>
                @isset($title)
                    <h1 class="text-xl font-semibold text-gray-800">{{ $title }}</h1>
                @endisset
                @isset($breadcrumb)
                    <nav class="text-sm text-gray-500 mt-0.5">{{ $breadcrumb }}</nav>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                @isset($actions)
                    {{ $actions }}
                @endisset
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 px-6 py-3 text-green-800 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 px-6 py-3 text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Page body --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
