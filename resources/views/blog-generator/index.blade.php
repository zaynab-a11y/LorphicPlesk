<x-app-layout title="Blog Generator">
@php
$domainLabel = function(string $d): string {
    if (str_starts_with($d, 'sc-domain:')) return substr($d, 10);
    return preg_replace('#^https?://(www\.)?#', '', rtrim($d, '/'));
};
@endphp

<div class="flex gap-5 min-h-0">

    {{-- LEFT: Form + Output + History --}}
    <div class="flex-1 min-w-0 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Blog Generator</h1>
                <p class="text-xs text-gray-400 mt-0.5">SearchFit SEO · AEO + GEO optimized</p>
            </div>
            @if($domain)
            <span class="text-xs px-3 py-1.5 rounded-full font-medium border" style="background:rgba(0,191,179,.08);border-color:rgba(0,191,179,.25);color:#00918a;">
                {{ $domainLabel($domain) }}
            </span>
            @endif
        </div>

        {{-- Generator Form --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4">Generate New Blog Post</h2>
            <form method="POST" action="{{ route('blog-generator.generate') }}" id="generate-form">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Client</label>
                        <select name="domain" required onchange="this.form.submit()" class="input-dark">
                            @foreach($clients as $c)
                            <option value="{{ $c->gsc_site_url }}" {{ $domain === $c->gsc_site_url ? 'selected' : '' }}>
                                {{ $domainLabel($c->gsc_site_url) }} — {{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Target Word Count</label>
                        <select name="word_count" class="input-dark">
                            <option value="1000">~1,000 words</option>
                            <option value="1500">~1,500 words</option>
                            <option value="2000" selected>~2,000 words</option>
                            <option value="2500">~2,500 words</option>
                            <option value="3000">~3,000 words</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Blog Topic</label>
                        <input type="text" name="topic" required placeholder="e.g. limo service for weddings in Aberdeen MD"
                            value="{{ old('topic') }}" class="input-dark">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Focus Keyword</label>
                        <input type="text" name="focus_keyword" required placeholder="e.g. wedding limo service Aberdeen MD"
                            value="{{ old('focus_keyword') }}" class="input-dark">
                        <p class="mt-1 text-xs text-gray-400">Used in H1, opening 100 words, 2× H2s, and conclusion</p>
                    </div>
                </div>
                <div class="mt-5">
                    <button type="submit" id="generate-btn" onclick="startGenerate()"
                        class="btn-primary inline-flex items-center gap-2">
                        <svg id="btn-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <svg id="btn-spinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span id="btn-text">Generate Blog</span>
                    </button>
                    <span class="ml-3 text-xs text-gray-400">~30–60 seconds · SearchFit SEO framework</span>
                </div>
            </form>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
        @endif

        {{-- Blog Output --}}
        @if($viewBlog)
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">{{ $viewBlog->topic }}</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs text-gray-500">{{ number_format($viewBlog->word_count_actual ?? 0) }} words</span>
                        <span class="text-xs text-gray-300">·</span>
                        <span class="text-xs text-gray-500">{{ $viewBlog->generated_at->format('M j, Y g:i A') }}</span>
                        <span class="text-xs text-gray-300">·</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $viewBlog->published_at ? 'text-emerald-700 bg-emerald-50' : 'text-gray-500 bg-gray-100' }}">
                            {{ $viewBlog->published_at ? 'Published '.$viewBlog->published_at->format('M j') : 'Draft' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="copyBlog()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span id="copy-label">Copy</span>
                    </button>
                    <form method="POST" action="{{ route('blog-generator.publish', $viewBlog) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors {{ $viewBlog->published_at ? 'border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $viewBlog->published_at ? 'Published' : 'Mark Published' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('blog-generator.destroy', $viewBlog) }}"
                        onsubmit="return confirm('Delete this blog post?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-red-100 text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            <div id="blog-content" class="blog-prose px-6 py-5" style="max-height:70vh;overflow-y:auto;">
                {!! $viewBlog->content !!}
            </div>
        </div>
        @endif

        {{-- History --}}
        @if($history->count())
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">Generated Blogs</h2>
                <span class="text-xs text-gray-400">Last 10 for {{ $domainLabel($domain) }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($history as $blog)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex-1 min-w-0 mr-4">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $blog->topic }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-400">{{ $blog->generated_at->format('M j, Y') }}</span>
                            <span class="text-xs text-gray-300">·</span>
                            <span class="text-xs text-gray-400">{{ number_format($blog->word_count_actual ?? 0) }} words</span>
                            @if($blog->published_at)
                            <span class="text-xs font-medium" style="color:#00918a;">· Published {{ $blog->published_at->format('M j') }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('blog-generator.index', ['domain' => $domain, 'view' => $blog->id]) }}"
                        class="flex-shrink-0 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors"
                        style="border-color:rgba(0,191,179,.3);color:#00918a;"
                        onmouseover="this.style.background='rgba(0,191,179,.08)'"
                        onmouseout="this.style.background=''">
                        View
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT: Published Memory Panel --}}
    <div class="w-60 flex-shrink-0 hidden xl:block">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 sticky top-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Published This Month</h3>
                <span class="text-xs text-gray-400">{{ now()->format('M Y') }}</span>
            </div>
            @if($publishedThisMonth->count())
            <div class="space-y-2">
                @foreach($publishedThisMonth as $pub)
                <a href="{{ route('blog-generator.index', ['domain' => $domain, 'view' => $pub->id]) }}"
                    class="block rounded-lg p-2.5 border border-gray-100 bg-gray-50 hover:border-teal-200 hover:bg-teal-50 transition-colors">
                    <p class="text-xs font-medium text-gray-800 leading-snug line-clamp-2">{{ $pub->topic }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $pub->published_at->format('M j') }}</p>
                </a>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 text-center">{{ $publishedThisMonth->count() }} post{{ $publishedThisMonth->count() === 1 ? '' : 's' }} published</p>
            </div>
            @else
            <div class="rounded-xl border border-dashed border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-400">No posts published this month yet.</p>
            </div>
            @endif
            @if($domain)
            @php $totalPublished = \App\Models\GeneratedBlog::where('domain', $domain)->whereNotNull('published_at')->count(); @endphp
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 mb-2">All Time</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">Total published</span>
                    <span class="text-xs font-bold text-gray-700">{{ $totalPublished }}</span>
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-xs text-gray-400">Total generated</span>
                    <span class="text-xs font-bold text-gray-700">{{ \App\Models\GeneratedBlog::where('domain', $domain)->count() }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

<style>
.blog-prose{font-family:'Inter',system-ui,sans-serif;font-size:14px;line-height:1.8;color:#1f2937}
.blog-prose h1{font-size:1.5rem;font-weight:800;color:#111827;margin:1.5rem 0 1rem;line-height:1.3}
.blog-prose h2{font-size:1.15rem;font-weight:700;color:#111827;margin:2rem 0 .75rem;padding-bottom:.4rem;border-bottom:2px solid #f0fdfb}
.blog-prose h3{font-size:1rem;font-weight:700;color:#00918a;margin:1.25rem 0 .5rem}
.blog-prose p{margin:.75rem 0}
.blog-prose ul,.blog-prose ol{margin:.75rem 0 .75rem 1.5rem}
.blog-prose li{margin:.4rem 0}
.blog-prose strong{font-weight:700;color:#111827}
.blog-prose a{color:#00918a;text-decoration:underline;font-weight:500}
.blog-prose a:hover{color:#007a74}
.blog-prose table{width:100%;border-collapse:collapse;margin:1.5rem 0;font-size:13px}
.blog-prose th{background:#f0fdfb;color:#00918a;font-weight:700;padding:10px 14px;text-align:left;border:1px solid #d1faf7}
.blog-prose td{padding:9px 14px;border:1px solid #e5e7eb;vertical-align:top}
.blog-prose tr:nth-child(even) td{background:#f9fafb}
.takeaways-box{background:linear-gradient(135deg,#f0fdfb,#ecfdf5);border:1.5px solid #99f6e4;border-radius:12px;padding:1rem 1.25rem;margin:1.25rem 0}
.takeaways-box::before{content:"✦ Key Takeaways";display:block;font-weight:800;font-size:13px;color:#00918a;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.6rem}
.takeaways-box ul{margin:0 0 0 1.25rem}
.takeaways-box li{font-size:13px;color:#1f2937;margin:.35rem 0}
.decision-framework{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1rem 1.25rem;margin:1.5rem 0}
.decision-framework::before{content:"⚖ Decision Framework";display:block;font-weight:800;font-size:13px;color:#374151;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem}
.faq-section{margin:1.5rem 0}
.faq-section::before{content:"❓ Frequently Asked Questions";display:block;font-size:1.1rem;font-weight:700;color:#111827;margin-bottom:1rem;padding-bottom:.4rem;border-bottom:2px solid #f0fdfb}
.faq-item{border:1px solid #e5e7eb;border-radius:10px;padding:.85rem 1rem;margin:.6rem 0;background:#fff}
.faq-item h3{margin:0 0 .4rem;font-size:.9rem;color:#111827;font-weight:700}
.faq-item p{margin:0;font-size:13px;color:#4b5563;line-height:1.6}
.steps-section{background:#fafafa;border-left:3px solid #00BFB3;padding:.75rem 1rem .75rem 1.25rem;margin:1.25rem 0;border-radius:0 8px 8px 0}
.last-updated{font-size:12px;color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem .75rem;margin-bottom:1rem}
.sources{font-size:12px;color:#6b7280;border-top:1px solid #f0f0f0;padding-top:.75rem;margin-top:1.5rem}
.sources a{font-size:12px}
.opening{font-size:15px;font-weight:500;color:#374151;line-height:1.75;border-left:3px solid #00BFB3;padding-left:.85rem;margin:1rem 0 1.25rem}
</style>

<script>
function startGenerate() {
    document.getElementById('btn-icon').classList.add('hidden');
    document.getElementById('btn-spinner').classList.remove('hidden');
    document.getElementById('btn-text').textContent = 'Generating…';
    document.getElementById('generate-btn').disabled = true;
}
function copyBlog() {
    const content = document.getElementById('blog-content');
    if (!content) return;
    navigator.clipboard.writeText(content.innerText).then(() => {
        const label = document.getElementById('copy-label');
        label.textContent = 'Copied!';
        setTimeout(() => label.textContent = 'Copy', 2000);
    });
}
</script>
</x-app-layout>
