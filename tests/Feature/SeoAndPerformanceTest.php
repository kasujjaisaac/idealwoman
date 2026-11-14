<?php

namespace Tests\Feature;

use App\Models\ContentSection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoAndPerformanceTest extends TestCase
{
    public function test_pages_have_distinct_titles_and_valid_structured_data(): void
    {
        $home = $this->get('/')->assertOk();
        $about = $this->get('/about')->assertOk();
        preg_match('/<title>(.*?)<\/title>/s', $home->getContent(), $homeTitle);
        preg_match('/<title>(.*?)<\/title>/s', $about->getContent(), $aboutTitle);
        $this->assertNotSame($homeTitle[1], $aboutTitle[1]);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $home->getContent(), $script);
        $data = json_decode($script[1], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertSame('Organization', $data['@graph'][0]['@type']);
        $this->assertContains('https://x.com/idealwomanugan1?s=11', $data['@graph'][0]['sameAs']);
        $this->get('/stories/learning-through-making')->assertOk()->assertSee('"@type":"Article"', false);
    }

    public function test_draft_preview_stays_unindexable_when_public_indexing_is_enabled(): void
    {
        config(['idealwoman.indexable' => true]);
        ContentSection::factory()->create(['slug' => 'home', 'draft' => ['text_010' => 'Private draft']]);

        $this->actingAs(User::factory()->editor()->create())->get('/?preview=home')
            ->assertOk()->assertSee('noindex, nofollow')->assertDontSee('index, follow, max-image-preview')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_uploaded_photos_get_responsive_webp_without_upscaling_and_keep_the_original(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('community.png', 1200, 800);

        $response = $this->actingAs(User::factory()->editor()->create())->post('/admin/media', ['file' => $file]);
        $response->assertRedirect()->assertSessionHas('uploaded');
        $path = session('uploaded');
        $name = pathinfo($path, PATHINFO_FILENAME);
        Storage::disk('local')->assertExists($path);
        $metadata = json_decode(Storage::disk('local')->get('cms/variants/'.$name.'.json'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame([640, 960, 1200], array_keys($metadata['variants']));
        $dimensions = getimagesize(Storage::disk('local')->path('cms/variants/'.$metadata['variants'][640]));
        $this->assertSame([640, 427], [$dimensions[0], $dimensions[1]]);
        $this->assertSame('image/webp', $dimensions['mime']);
        $this->get('/media/'.$metadata['variants'][640])->assertOk()->assertHeader('Content-Type', 'image/webp')
            ->assertHeader('Cache-Control', 'max-age=2592000, public');
        $this->get('/media/'.$name.'.json')->assertNotFound();
        $this->get('/media/missing-640.webp')->assertNotFound();
    }

    public function test_pdf_uploads_are_preserved_without_image_processing(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->editor()->create())->post('/admin/media', [
            'file' => UploadedFile::fake()->createWithContent('profile.pdf', "%PDF-1.4\n%%EOF"),
        ])->assertRedirect()->assertSessionHas('uploaded');

        Storage::disk('local')->assertExists(session('uploaded'));
        $this->assertCount(1, Storage::disk('local')->allFiles('cms'));
    }

    public function test_oversized_image_dimensions_are_rejected_before_storage(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->editor()->create())->post('/admin/media', [
            'file' => UploadedFile::fake()->image('too-wide.png', 10001, 1),
        ])->assertSessionHasErrors(['file' => 'Choose a valid image with no more than 12 megapixels and 10,000 pixels on either side.']);

        Storage::disk('local')->assertDirectoryEmpty('cms');
    }

    public function test_private_media_metadata_cannot_be_requested_as_an_image(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('cms/variants/private.json', '{"width":1200}');

        $this->get('/media/private.json')->assertNotFound();
        $this->get('/media/%2e%2e%2fprivate.json')->assertNotFound();
    }

    public function test_search_verification_and_slideshow_controls_are_rendered(): void
    {
        config(['idealwoman.google_site_verification' => 'verification-token']);

        $this->get('/')->assertOk()->assertSee('name="google-site-verification" content="verification-token"', false)
            ->assertSee('Pause slideshow')->assertSee('data-srcset=', false)->assertSee('fetchpriority="high"', false);
    }
}
