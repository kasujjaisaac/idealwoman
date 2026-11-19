<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;

class WebsiteTranslator
{
    /** @param array<int, string> $texts
     * @return array<int, string>
     */
    public function translate(array $texts, string $language): array
    {
        abort_if(blank(config('services.translation.key')), 503, 'Automatic translation is not configured.');
        $translations = [];
        $missing = [];
        foreach (array_unique($texts) as $text) {
            $cached = Cache::get($this->cacheKey($text, $language));
            if (is_string($cached)) {
                $translations[$text] = $cached;
            } else {
                $missing[] = $text;
            }
        }
        if ($missing !== []) {
            $characters = array_sum(array_map(mb_strlen(...), $missing));
            $used = RateLimiter::increment('website-translations:'.now()->format('Y-m-d'), 86400, $characters);
            abort_if($used > (int) config('services.translation.daily_character_limit'), 429, 'Translation usage limit reached.');
            $response = Http::acceptJson()->withHeaders(['X-Goog-Api-Key' => config('services.translation.key')])
                ->connectTimeout(5)->timeout(20)
                ->post('https://translation.googleapis.com/language/translate/v2', [
                    'q' => $missing, 'source' => 'en', 'target' => $language, 'format' => 'text',
                ]);
            $results = $response->json('data.translations');
            if (! $response->successful() || ! is_array($results) || count($results) !== count($missing)) {
                throw new RuntimeException('The translation provider returned an invalid response.');
            }
            foreach ($results as $index => $result) {
                if (! is_string($result['translatedText'] ?? null)) {
                    throw new RuntimeException('The translation provider returned an invalid translation.');
                }
                $translations[$missing[$index]] = html_entity_decode($result['translatedText'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            foreach ($missing as $text) {
                Cache::put($this->cacheKey($text, $language), $translations[$text], now()->addDays(30));
            }
        }

        return array_map(fn (string $text): string => $translations[$text], $texts);
    }

    private function cacheKey(string $text, string $language): string
    {
        return 'website-translation:v1:'.$language.':'.hash('sha256', $text);
    }
}
