@props(['title' => null])
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Lorphic</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        *{font-family:'Inter',system-ui,sans-serif;}
        ::-webkit-scrollbar{width:4px;height:4px;}
        ::-webkit-scrollbar-track{background:#f0f0f0;}
        ::-webkit-scrollbar-thumb{background:#00BFB3;border-radius:2px;}

        /* Sidebar nav links */
        .nav-link{transition:all .15s;border-left:2px solid transparent;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:0 8px 8px 0;font-size:.8125rem;color:#9ca3af;}
        .nav-link.active{border-left-color:#00BFB3;background:rgba(0,191,179,.15);color:#fff;}
        .nav-link:not(.active):hover{background:rgba(255,255,255,.06);color:#e2e8f0;}

        /* Cards */
        .stat-card{transition:transform .2s,box-shadow .2s;cursor:default;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(0,191,179,.1);}

        /* Animations */
        @keyframes fadeDown{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
        .fade-down{animation:fadeDown .15s ease-out;}
        @keyframes pulse2{0%,100%{opacity:1}50%{opacity:.3}}
        .live-dot{animation:pulse2 1.8s infinite;}
        .sidebar-tr{transition:transform .22s cubic-bezier(.4,0,.2,1);}

        /* Buttons */
        .btn-primary{background:#00BFB3;color:#000;font-weight:600;padding:8px 18px;border-radius:8px;font-size:.8125rem;transition:opacity .15s;}
        .btn-primary:hover{opacity:.88;}

        /* Inputs — light theme */
        .input-dark{width:100%;padding:9px 14px;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb;color:#111827;font-size:.8125rem;outline:none;transition:border-color .15s;}
        .input-dark:focus{border-color:#00BFB3;background:#fff;}
        .input-dark::placeholder{color:#9ca3af;}

        /* Table rows */
        .table-row{transition:background .12s;}
        .table-row:hover{background:#f8fafc;}
    </style>
</head>
<body class="h-full" style="background:#ffffff;color:#111827;">

<div id="overlay" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

<div class="flex h-full min-h-screen">

    <!-- SIDEBAR — black -->
    <aside id="sidebar" class="sidebar-tr fixed lg:static inset-y-0 left-0 z-50 w-56 flex flex-col -translate-x-full lg:translate-x-0 flex-shrink-0" style="background:#0d0f14;border-right:1px solid rgba(255,255,255,.06);">

        <div class="flex items-center justify-center px-4 h-14 flex-shrink-0" style="border-bottom:1px solid rgba(255,255,255,.06);">
            <img src="/logo.png" alt="Lorphic" style="max-height:36px;max-width:140px;object-fit:contain;">
        </div>

        <div class="px-4 py-3 flex-shrink-0" style="border-bottom:1px solid rgba(255,255,255,.06);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-black flex-shrink-0" style="background:linear-gradient(135deg,#00BFB3,#009e92);">
                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs truncate" style="color:#00BFB3;">{{ Auth::user()->isAdmin() ? 'Administrator' : 'Client' }}</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            @if(Auth::user()->canSeeTab('rankings'))
            <a href="{{ route('rankings.index') }}" class="nav-link {{ request()->routeIs('rankings.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Rankings
            </a>
            @endif

            @if(Auth::user()->canSeeTab('ranking_screenshots'))
            <a href="{{ route('ranking-screenshots.index') }}" class="nav-link {{ request()->routeIs('ranking-screenshots.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Ranking SS
            </a>
            @endif

            @if(Auth::user()->canSeeTab('reports'))
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reports
            </a>
            @endif

            @if(Auth::user()->canSeeTab('gsc'))
            <a href="{{ route('gsc.index') }}" class="nav-link {{ request()->routeIs('gsc.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                Search Console
            </a>
            @endif

            @if(Auth::user()->canSeeTab('backlinks'))
            <a href="{{ route('backlinks.index') }}" class="nav-link {{ request()->routeIs('backlinks.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Backlinks
            </a>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->canSeeTab('opportunities'))
            <a href="{{ route('opportunities.index') }}" class="nav-link {{ request()->routeIs('opportunities.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Opportunities
            </a>
            @endif

            <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Chat
                <span id="chat-nav-badge" class="hidden ml-auto w-5 h-5 rounded-full text-white text-xs flex items-center justify-center font-bold" style="background:#ef4444;font-size:9px;line-height:1;"></span>
            </a>

            @if(Auth::user()->isAdmin())
            <div class="pt-3 mt-1" style="border-top:1px solid rgba(255,255,255,.06);">
                <div class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider" style="color:#4b5563;">Admin</div>
                <a href="{{ route('targets.index') }}" class="nav-link {{ request()->routeIs('targets.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Weekly Targets
                </a>
                <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Users
                </a>
                <a href="{{ route('admin.activity') }}" class="nav-link {{ request()->routeIs('admin.activity') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    All Activity
                </a>
                <a href="{{ route('rank-checker.index') }}" class="nav-link {{ request()->routeIs('rank-checker.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Rank Checker
                </a>
            </div>
            @endif
        </nav>

        <div class="px-2 py-3 flex-shrink-0" style="border-top:1px solid rgba(255,255,255,.06);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-full" style="color:#6b7280;" onmouseover="this.style.color='#f87171';this.style.background='rgba(239,68,68,.08)';" onmouseout="this.style.color='#6b7280';this.style.background='';">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <header class="flex items-center justify-between px-5 h-14 flex-shrink-0" style="background:#fff;border-bottom:1px solid #e5e7eb;position:sticky;top:0;z-index:30;">
            <div class="flex items-center gap-3">
                <button onclick="openSidebar()" class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="font-semibold text-sm text-gray-900">{{ $title ?? 'Dashboard' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium" style="background:rgba(0,191,179,.1);color:#00918a;">
                    <span class="w-1.5 h-1.5 rounded-full live-dot" style="background:#00BFB3;"></span>
                    Live
                </div>
                <div class="relative">
                    <button id="bell-btn" onclick="toggleNotif()" class="relative p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span id="notif-badge" class="hidden absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-white font-bold flex items-center justify-center" style="background:#00BFB3;font-size:9px;line-height:1;color:#000;"></span>
                    </button>
                    <div id="notif-panel" class="hidden absolute right-0 top-full mt-2 w-72 sm:w-80 max-w-[calc(100vw-1.5rem)] rounded-xl border border-gray-200 shadow-lg z-50 fade-down overflow-hidden bg-white">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                            <span class="text-sm font-semibold text-gray-900">Recent Activity</span>
                            <a href="{{ route('activity.index') }}" class="text-xs font-medium" style="color:#00BFB3;">See all →</a>
                        </div>
                        <div id="notif-list" class="max-h-72 overflow-y-auto">
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">Loading…</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
        <div id="flash" class="mx-5 mt-4 flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium border" style="background:#f0fdf4;color:#166534;border-color:#bbf7d0;">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-5 mt-4 px-4 py-3 rounded-xl text-sm font-medium border" style="background:#fef2f2;color:#991b1b;border-color:#fecaca;">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="mx-5 mt-4 px-4 py-3 rounded-xl text-sm font-medium border" style="background:#fef2f2;color:#991b1b;border-color:#fecaca;">{{ $errors->first() }}</div>
        @endif

        <main class="flex-1 overflow-y-auto p-3 sm:p-5">{{ $slot }}</main>
    </div>
</div>

<script>
function openSidebar(){document.getElementById('sidebar').classList.remove('-translate-x-full');document.getElementById('overlay').classList.remove('hidden');}
function closeSidebar(){document.getElementById('sidebar').classList.add('-translate-x-full');document.getElementById('overlay').classList.add('hidden');}
let notifOpen=false;
function toggleNotif(){
    notifOpen=!notifOpen;
    const p=document.getElementById('notif-panel');
    p.classList.toggle('hidden',!notifOpen);
    if(notifOpen)fetchNotifs();
}
document.addEventListener('click',e=>{
    if(notifOpen&&!e.target.closest('#notif-panel')&&!e.target.closest('#bell-btn')){
        document.getElementById('notif-panel').classList.add('hidden');notifOpen=false;
    }
});
function fetchNotifs(){
    fetch('{{ route("notifications.recent") }}',{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
    .then(r=>r.json()).then(items=>{
        const list=document.getElementById('notif-list');
        const badge=document.getElementById('notif-badge');
        if(!items||!items.length){list.innerHTML='<div class="px-4 py-8 text-center text-gray-400 text-sm">No recent activity</div>';return;}
        badge.textContent=items.length>9?'9+':items.length;badge.classList.remove('hidden');
        list.innerHTML=items.map(n=>`<div class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors"><div class="flex items-start gap-2"><div class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style="background:#00BFB3;"></div><div class="min-w-0"><div class="text-xs font-medium text-gray-900 truncate">${esc(n.title||n.type||'Activity')}</div>${n.description?`<div class="text-xs text-gray-500 mt-0.5 line-clamp-1">${esc(n.description)}</div>`:''}<div class="text-xs text-gray-400 mt-1">${ago(n.created_at)}</div></div></div></div>`).join('');
    }).catch(()=>{document.getElementById('notif-list').innerHTML='<div class="px-4 py-6 text-center text-gray-400 text-sm">Could not load</div>';});
}
function esc(s){return s?String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'):''}
function ago(d){const s=Math.floor((Date.now()-new Date(d))/1000);if(s<60)return s+'s ago';if(s<3600)return Math.floor(s/60)+'m ago';if(s<86400)return Math.floor(s/3600)+'h ago';return Math.floor(s/86400)+'d ago';}
setTimeout(()=>{const f=document.getElementById('flash');if(f){f.style.transition='opacity .5s';f.style.opacity='0';setTimeout(()=>f.remove(),500);}},4000);
</script>
@isset($scripts){{ $scripts }}@endisset
</body>
</html>
