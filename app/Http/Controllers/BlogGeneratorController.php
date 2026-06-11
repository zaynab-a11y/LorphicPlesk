<?php

namespace App\Http\Controllers;

use App\Models\GeneratedBlog;
use App\Models\User;
use App\Services\BlogGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogGeneratorController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $clients = User::whereNotNull('gsc_site_url')->orderBy('name')->get();
        $domain  = $request->get('domain') ?: ($clients->first()?->gsc_site_url ?? null);

        $history = $domain
            ? GeneratedBlog::where('domain', $domain)->latest('generated_at')->limit(10)->get()
            : collect();

        $publishedThisMonth = $domain
            ? GeneratedBlog::where('domain', $domain)
                ->whereNotNull('published_at')
                ->whereYear('published_at', now()->year)
                ->whereMonth('published_at', now()->month)
                ->orderBy('published_at', 'desc')
                ->get()
            : collect();

        $viewBlog = $request->get('view')
            ? GeneratedBlog::find($request->get('view'))
            : null;

        return view('blog-generator.index', compact(
            'clients', 'domain', 'history', 'publishedThisMonth', 'viewBlog'
        ));
    }

    public function generate(Request $request, BlogGeneratorService $svc)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $request->validate([
            'domain'        => 'required|string',
            'topic'         => 'required|string|max:500',
            'focus_keyword' => 'required|string|max:200',
            'word_count'    => 'nullable|integer|min:500|max:5000',
        ]);

        $domain       = $request->input('domain');
        $topic        = $request->input('topic');
        $focusKeyword = $request->input('focus_keyword');
        $wordCount    = (int) ($request->input('word_count') ?? 2000);

        $blog = GeneratedBlog::create([
            'user_id'           => Auth::id(),
            'domain'            => $domain,
            'topic'             => $topic,
            'focus_keyword'     => $focusKeyword,
            'word_count_target' => $wordCount,
            'status'            => 'pending',
            'generated_at'      => now(),
        ]);

        // Spawn background CLI process — independent of this web request
        $php     = '/opt/plesk/php/8.4/bin/php';
        $artisan = base_path('artisan');
        $log     = storage_path('logs/blog-generate.log');
        shell_exec("nohup {$php} {$artisan} blog:generate {$blog->id} >> {$log} 2>&1 &");

        return redirect()->route('blog-generator.index', ['domain' => $domain, 'view' => $blog->id]);
    }

    public function markPublished(Request $request, GeneratedBlog $blog)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $blog->update(['published_at' => $blog->published_at ? null : now()]);
        return redirect()->route('blog-generator.index', ['domain' => $blog->domain, 'view' => $blog->id]);
    }

    public function destroy(GeneratedBlog $blog)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $domain = $blog->domain;
        $blog->delete();
        return redirect()->route('blog-generator.index', ['domain' => $domain]);
    }
}
