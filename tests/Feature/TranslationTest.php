<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    public function test_unconfigured_translation_returns_a_clear_error_without_contacting_a_provider(): void
    {
        config(['services.translation.key' => null]);
        Http::fake();

        $this->postJson('/translate', ['language' => 'fr', 'texts' => ['A newly published paragraph.']])->assertStatus(503);

        Http::assertNothingSent();
    }

    public function test_new_content_is_translated_and_repeated_requests_use_the_cache(): void
    {
        config(['services.translation.key' => 'test-key']);
        Http::fake(['translation.googleapis.com/*' => Http::response(['data' => ['translations' => [['translatedText' => 'Un nouveau paragraphe.']]]])]);

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->postJson('/translate', ['language' => 'fr', 'texts' => ['A newly published paragraph.']])
                ->assertOk()->assertExactJson(['translations' => ['Un nouveau paragraphe.']]);
        }

        Http::assertSentCount(1);
        Http::assertSent(fn ($request): bool => $request->hasHeader('X-Goog-Api-Key', 'test-key') && $request['source'] === 'en' && $request['target'] === 'fr' && $request['q'] === ['A newly published paragraph.']);
    }

    public function test_changed_content_and_other_languages_do_not_reuse_stale_translations(): void
    {
        config(['services.translation.key' => 'test-key']);
        Http::fake(fn ($request) => Http::response(['data' => ['translations' => [['translatedText' => $request['target'].':'.$request['q'][0]]]]]));

        foreach ([['fr', 'First paragraph.'], ['sw', 'First paragraph.'], ['fr', 'An edited paragraph.']] as [$language, $text]) {
            $this->postJson('/translate', ['language' => $language, 'texts' => [$text]])
                ->assertOk()->assertJsonPath('translations.0', $language.':'.$text);
        }

        Http::assertSentCount(3);
    }

    public function test_invalid_input_and_oversized_batches_never_reach_the_provider(): void
    {
        config(['services.translation.key' => 'test-key']);
        Http::fake();

        $this->postJson('/translate', ['language' => 'unsupported', 'texts' => ['Hello']])->assertUnprocessable()->assertJsonValidationErrors('language');
        $this->postJson('/translate', ['language' => 'fr', 'texts' => array_fill(0, 31, 'Hello')])->assertUnprocessable()->assertJsonValidationErrors('texts');
        $this->postJson('/translate', ['language' => 'fr', 'texts' => [str_repeat('a', 3001)]])->assertUnprocessable()->assertJsonValidationErrors('texts.0');
        $this->postJson('/translate', ['language' => 'fr', 'texts' => array_fill(0, 5, str_repeat('a', 3000))])->assertUnprocessable()->assertJsonValidationErrors('texts');

        Http::assertNothingSent();
    }

    public function test_provider_errors_do_not_expose_credentials_or_cache_a_failed_result(): void
    {
        config(['services.translation.key' => 'private-test-key']);
        Http::fake(['translation.googleapis.com/*' => Http::response(['error' => 'private-test-key'], 403)]);

        $this->postJson('/translate', ['language' => 'fr', 'texts' => ['New information.']])
            ->assertStatus(503)->assertDontSee('private-test-key');

        Http::fake(['translation.googleapis.com/*' => Http::response(['data' => ['translations' => [['translatedText' => 'De nouvelles informations.']]]])]);
        $this->postJson('/translate', ['language' => 'fr', 'texts' => ['New information.']])
            ->assertOk()->assertJsonPath('translations.0', 'De nouvelles informations.');
        Http::assertSentCount(1);
    }

    public function test_daily_character_budget_limits_uncached_usage(): void
    {
        config(['services.translation.key' => 'test-key', 'services.translation.daily_character_limit' => 3]);
        Http::fake();

        $this->postJson('/translate', ['language' => 'fr', 'texts' => ['More than three characters.']])->assertTooManyRequests();

        Http::assertNothingSent();
    }

    public function test_partial_provider_responses_are_rejected(): void
    {
        config(['services.translation.key' => 'test-key']);
        Http::fake(['translation.googleapis.com/*' => Http::response(['data' => ['translations' => []]])]);

        $this->postJson('/translate', ['language' => 'fr', 'texts' => ['Hello']])->assertStatus(503);
    }
}
