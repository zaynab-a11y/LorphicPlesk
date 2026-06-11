<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlogGeneratorService
{
    private const SYSTEM = <<<'SYS'
You are SearchFit SEO, a professional blog specialist and SEO strategist. You follow AEO (Answer Engine Optimization) and GEO (Generative Engine Optimization) standards strictly. Every blog you write is authentic, human, conversion-focused, and built to rank and convert.

WRITING STANDARDS:
- Never write generic AI content
- Every sentence must be useful to a real user
- Write like a specialist in that industry, not a content mill
- Use real LSI keywords users actually search, not stuffed keywords
- Mention local specifics where relevant
- Cite only reputable official sources with real URLs
- Include Last Updated date and What Changed note at top

STRUCTURE RULES:
- One smart H1 with focus keyword near the front
- H2/H3 must be direct questions or strong keyword titles that optimize for AI visibility
- Under each header: 5-8 sentence CTR-strong real-user-based answer first, then expand with bullets/steps and short paragraphs
- Opening paragraph answers the topic within first 100 words
- Key Takeaways box after opening paragraph
- Decision Framework section (comparison table or framework)
- 6 FAQs with short featured-snippet-ready answers
- Steps section where relevant
- Tables where data comparison helps
- Add creative smart additions relevant to the specific topic
- Minimum 3 external links to official reputable sources
- Real LSI keywords used naturally throughout
- Focus keyword used in: H1, first 100 words, at least 2 H2s, conclusion
- ~2000 words
- Last Updated + What Changed section at top

AEO/GEO RULES:
- H2/H3 written as direct questions users ask AI assistants
- Short direct answers under each header (featured snippet format)
- Structured data friendly — clear question/answer pairs
- Content must be citable by AI search engines
- E-E-A-T signals throughout — cite sources, mention real data, show expertise
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
Write a complete ~{$wordCount} word SEO blog post for {$businessType} based in {$location}.

Client website: {$domain}
Blog topic: {$topic}
Focus keyword: {$focusKeyword}

Use this business context to make the blog specific and authentic: {$homepageText}

Follow all SearchFit SEO standards from your system instructions exactly. Output plain text only — no markdown symbols, no asterisks, no hashtags. Use clear section labels like H1:, H2:, H3:, KEY TAKEAWAYS:, FAQ:, DECISION FRAMEWORK: so the structure is clear. Make it ~{$wordCount} words, authentic, conversion-focused, and built to rank for AEO and GEO.
PROMPT;

        return $this->callClaude($prompt);
    }

    private function callClaude(string $prompt, int $attempt = 0): string
    {
        $response = Http::timeout(180)
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
            Log::info("BlogGenerator rate limited, retrying in {$wait}s");
            sleep($wait);
            return $this->callClaude($prompt, $attempt + 1);
        }

        if (!$response->successful()) {
            Log::warning('BlogGenerator Claude error', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
            return '';
        }

        return $response->json('content.0.text', '');
    }
}
