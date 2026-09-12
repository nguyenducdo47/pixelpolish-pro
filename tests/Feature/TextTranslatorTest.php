<?php

namespace Tests\Feature;

use App\Filament\Forms\RichEditor\FullEditorPlugin;
use App\Models\TranslationApi;
use App\Services\TextTranslator;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class TextTranslatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('studio'));
    }

    public function test_google_chrome_is_used_first(): void
    {
        Http::fake([
            'clients5.google.com/*' => Http::response([
                ['Hello world', 'vi'],
            ], 200),
            'translate.googleapis.com/*' => Http::response('should-not-run', 500),
            'api.mymemory.translated.net/*' => Http::response('should-not-run', 500),
        ]);

        $this->assertSame(
            'Hello world',
            app(TextTranslator::class)->translate('Xin chào thế giới', 'en'),
        );

        Http::assertSent(fn ($request) => str_contains($request->url(), 'clients5.google.com'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'mymemory'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'googleapis.com'));
    }

    public function test_google_is_used_when_chrome_fails(): void
    {
        Http::fake([
            'clients5.google.com/*' => Http::response('rate limited', 429),
            'api.mymemory.translated.net/*' => Http::response(['responseStatus' => 403], 200),
            'translate.googleapis.com/*' => Http::response([
                [['Hello', 'Xin chào']],
            ], 200),
        ]);

        $this->assertSame('Hello', app(TextTranslator::class)->translate('Xin chào', 'en', 'vi'));
    }

    public function test_google_chrome_nested_payload_is_parsed(): void
    {
        Http::fake([
            'api.mymemory.translated.net/*' => Http::response(['responseStatus' => 429], 429),
            'translate.googleapis.com/*' => Http::response('rate limited', 429),
            'clients5.google.com/*' => Http::response([
                ['Hello world', 'vi'],
            ], 200),
        ]);

        $this->assertSame(
            'Hello world',
            app(TextTranslator::class)->translate('Xin chào thế giới', 'en', 'vi'),
        );
    }

    public function test_long_text_is_posted_to_mymemory(): void
    {
        TranslationApi::query()->create([
            'name' => 'MyMemory',
            'driver' => 'mymemory',
            'method' => 'POST',
            'url' => 'https://api.mymemory.translated.net/get',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        Http::fake([
            'api.mymemory.translated.net/*' => Http::response([
                'responseStatus' => 200,
                'responseData' => ['translatedText' => 'Hello'],
            ], 200),
        ]);

        $text = trim(str_repeat('Xin chào thế giới. ', 30));

        $this->assertSame('Hello Hello', app(TextTranslator::class)->translate($text, 'en', 'vi'));

        Http::assertSent(fn ($request) => $request->method() === 'POST');
    }

    public function test_empty_text_is_not_sent_to_the_api(): void
    {
        Http::fake();

        $this->assertSame('', app(TextTranslator::class)->translate('   ', 'en'));
        Http::assertNothingSent();
    }

    public function test_same_language_returns_the_original_text(): void
    {
        Http::fake();

        $this->assertSame('Hello', app(TextTranslator::class)->translate('Hello', 'en', 'en'));
        Http::assertNothingSent();
    }

    public function test_it_fails_when_both_providers_fail(): void
    {
        Http::fake([
            'translate.googleapis.com/*' => Http::response('error', 500),
            'clients5.google.com/*' => Http::response('error', 500),
            'api.mymemory.translated.net/*' => Http::response(['responseStatus' => 403], 200),
        ]);

        $this->expectException(RuntimeException::class);

        app(TextTranslator::class)->translate('Xin chào', 'en');
    }

    public function test_configured_database_api_is_used_instead_of_defaults(): void
    {
        TranslationApi::query()->create([
            'name' => 'Custom Memory',
            'driver' => 'mymemory',
            'method' => 'POST',
            'url' => 'https://translate.example.test/get',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        Http::fake([
            'translate.example.test/*' => Http::response([
                'responseStatus' => 200,
                'responseData' => ['translatedText' => 'From database'],
            ], 200),
            'api.mymemory.translated.net/*' => Http::response('should-not-run', 500),
        ]);

        $this->assertSame('From database', app(TextTranslator::class)->translate('Xin chào', 'en', 'vi'));
        Http::assertSent(fn ($request) => str_contains($request->url(), 'translate.example.test'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'mymemory.translated.net'));
    }

    public function test_rich_editor_exposes_the_translate_tool(): void
    {
        $names = collect(app(FullEditorPlugin::class)->getEditorTools())
            ->map(fn ($tool) => $tool->getName())
            ->all();

        $this->assertContains('translateSelection', $names);
        $this->assertContains('translateSelection', collect(app(FullEditorPlugin::class)->getEditorActions())->map->getName()->all());
    }
}
