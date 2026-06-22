<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogGeneratorService
{
    private const SYSTEM = <<<'SYS'
You are SearchFit SEO, an elite blog writer and SEO strategist specializing in AEO (Answer Engine Optimization) and GEO (Generative Engine Optimization). You write authentic, human, conversion-focused blogs that rank and convert.

OUTPUT FORMAT: Pure semantic HTML only. No markdown. No code fences. No plain-text labels like "H1:" or "H2:". Output only HTML body content using: <h1>, <h2>, <h3>, <p>, <ul>, <ol>, <li>, <strong>, <em>, <a href="URL" target="_blank" rel="noopener noreferrer">, <table>, <thead>, <tbody>, <tr>, <th>, <td>, and these special divs: <div class="takeaways-box">, <div class="decision-framework">, <div class="faq-section">, <div class="faq-item">, <div class="steps-section">.

MANDATORY STRUCTURE (in this exact order):
1. <p class="last-updated">Last Updated: [Month Year] | What Changed: [brief note about what was added/updated]</p>
2. <h1> — One smart H1 with focus keyword in first 5 words. Specific, compelling, not clickbait.
3. <p class="opening"> — Opening paragraph (100+ words) that directly answers the topic in the first 100 words. Include focus keyword naturally. Make it genuinely useful.
4. <div class="takeaways-box"><ul> with 4-5 <li> — Key Takeaways: the most useful, specific facts/insights a user needs to know
5. Multiple <h2> sections — Each H2 must be a direct question real users ask AI assistants OR a strong keyword-rich title optimized for featured snippets. Under each H2: write 5-8 sentences of CTR-strong, real-user-based content first (no fluff), then expand with <ul> bullets or <ol> steps and short <p> paragraphs. Use real LSI keywords users actually search.
6. <div class="decision-framework"> containing a <table> with real comparative data — options, costs, pros/cons, or other relevant comparison
7. <div class="steps-section"><ol> — Step-by-step section where relevant to the topic
8. <div class="faq-section"> with exactly 6 <div class="faq-item"> — each must have <h3> as the question and <p> as a short featured-snippet-ready answer (2-3 sentences max, direct and specific)
9. Conclusion <p> with focus keyword used naturally, ending with a business-relevant CTA
10. <p class="sources"><strong>Sources:</strong> followed by 3+ <a href="REAL_URL"> links to reputable official sources only (government sites, major industry publications, official org sites — verified real URLs only)

CONTENT RULES:
- Focus keyword must appear in: H1, first 100 words, at least 2 H2s, conclusion
- H2s written as real user queries for maximum AI/featured snippet visibility
- Real LSI keywords users actually search — naturally woven in, never stuffed
- Mention local specifics from the business context provided
- Every sentence must be genuinely useful to a real human user
- At least one data-rich comparison table
- E-E-A-T signals throughout: cite data, show expertise, be specific
- Write like an industry specialist, not a content mill
- ~2000 words total
SYS;

    public function fetchHomepageText(string $domain): string
    {
        try {
            $url  = str_starts_with($domain, 'http') ? $domain : "https://{$domain}";
            $resp = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
            if ($resp->successful()) {
                $text = preg_replace('/\s+/', ' ', strip_tags($resp->body()));
                return substr(trim($text), 0, 500);
            }
        } catch (\Exception $e) {
            // silent
        }
        return "Business website: {$domain}";
    }

    public function generate(
        string $domain,
        string $businessType,
        string $location,
        string $topic,
        string $focusKeyword,
        int    $wordCount,
        string $homepageText
    ): string {
        $prompt = <<<PROMPT
Write a complete ~{$wordCount} word SEO blog post in HTML for {$businessType} based in {$location}.

Client website: {$domain}
Blog topic: {$topic}
Focus keyword: {$focusKeyword}
Business context (use for authentic local specifics): {$homepageText}

Output clean semantic HTML body content ONLY — no <html>, <head>, <body> wrapper tags, no markdown, no code fences, no plain-text labels. Follow ALL mandatory structure and content rules from your system instructions exactly. Make every section specific to this business, authentic, conversion-focused, and built for AEO and GEO.
PROMPT;

        return $this->callClaude($prompt);
    }

    private function callClaude(string $prompt, int $attempt = 0): string
    {
        $response = Http::timeout(120)
            ->withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 4096,
                'system'     => self::SYSTEM,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

        if ($response->status() === 429 && $attempt < 2) {
            $wait = $attempt === 0 ? 20 : 40;
            Log::info("BlogGenerator rate limited, retrying in {$wait}s", ['attempt' => $attempt + 1]);
            sleep($wait);
            return $this->callClaude($prompt, $attempt + 1);
        }

        if (!$response->successful()) {
            Log::warning('BlogGenerator Claude error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return '';
        }

        return $response->json('content.0.text', '');
    }
}
