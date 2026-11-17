<?php

namespace Tests\Feature;

use App\Models\ContentSection;
use App\Models\User;
use App\Services\SiteContent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminEditingWorkflowTest extends TestCase
{
    private function payload(string $section, array $changes = [], string $action = 'publish', int $version = 0): array
    {
        return ['fields' => array_replace((new SiteContent)->defaults($section), $changes), 'action' => $action, 'version' => $version];
    }

    public function test_page_editor_groups_fields_and_exposes_related_controls(): void
    {
        $this->actingAs(User::factory()->editor()->create())->get('/admin/content/home')->assertOk()
            ->assertSee('Hero message and buttons')->assertSee('Section visibility')->assertSee('Page photographs')
            ->assertSee('name="fields[text_010]"', false)->assertSee('name="fields[hero_image_1]"', false)
            ->assertSee('Save and preview')->assertSee('Choose a photograph')->assertSee('/admin/content/programme-climate-change');
        $this->get('/admin')->assertOk()->assertSee('What would you like to change?')->assertSee('/admin/content/galleries');
    }

    public function test_save_and_preview_saves_a_private_revision_and_preserves_the_live_page(): void
    {
        $this->actingAs(User::factory()->editor()->create());
        $this->put('/admin/content/home', $this->payload('home', ['text_010' => 'Live heading.']));

        $response = $this->put('/admin/content/home', $this->payload('home', ['text_010' => 'Preview heading.'], 'preview', 1));

        $response->assertRedirect('/?preview=home');
        $section = ContentSection::where('slug', 'home')->firstOrFail();
        $this->assertSame('Live heading.', $section->published['text_010']);
        $this->assertSame('Preview heading.', $section->draft['text_010']);
        $this->assertDatabaseHas('content_revisions', ['content_section_id' => $section->id, 'action' => 'draft']);
        $this->get('/?preview=home')->assertOk()->assertSee('Preview heading.')->assertSee('Return to editor')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->post('/admin/logout');
        $this->get('/')->assertOk()->assertSee('Live heading.')->assertDontSee('Preview heading.')->assertDontSee('Website editor');
        $this->get('/?preview=home')->assertForbidden();
    }

    public function test_inline_upload_can_be_selected_without_publishing_until_the_editor_saves(): void
    {
        Storage::fake('local');
        $this->actingAs(User::factory()->editor()->create());

        $response = $this->postJson('/admin/media', ['file' => UploadedFile::fake()->image('Hero gathering.png', 800, 600)]);

        $response->assertCreated()->assertJsonPath('label', 'Hero gathering.png')->assertJsonPath('is_image', true);
        Storage::disk('local')->assertExists($response->json('path'));
        $name = $response->json('name');
        $this->assertDatabaseCount('content_sections', 0);
        $this->get('/')->assertDontSee($name);
        $this->put('/admin/content/home', $this->payload('home', ['hero_image_1' => $name, 'seo_image' => $name]))->assertRedirect();
        $this->get('/')->assertOk()->assertSee('/media/'.$name)->assertSee('srcset=', false);
        $this->assertSame($name, ContentSection::where('slug', 'home')->firstOrFail()->published['hero_image_1']);
    }

    public function test_inline_uploads_require_content_access_and_reject_executables(): void
    {
        Storage::fake('local');
        $this->postJson('/admin/media', [])->assertUnauthorized();
        $this->actingAs(User::factory()->create())->postJson('/admin/media', [])->assertForbidden();
        $this->actingAs(User::factory()->editor()->create())->postJson('/admin/media', ['file' => UploadedFile::fake()->createWithContent('bad.php', '<?php echo 1;')])
            ->assertUnprocessable()->assertJsonValidationErrors('file');
        Storage::disk('local')->assertDirectoryEmpty('cms');
    }

    public function test_gallery_images_and_captions_are_editable_and_can_be_removed(): void
    {
        $this->actingAs(User::factory()->editor()->create());

        $this->put('/admin/content/galleries', $this->payload('galleries', [
            'climate_1_image' => '', 'climate_5_image' => 'team', 'climate_5_alt' => '<script>New climate caption</script>',
            'peace_1_alt' => 'Updated peace caption.',
        ]))->assertRedirect();

        $this->get('/our-work')->assertOk()->assertDontSee('climate-biomass-')
            ->assertSee('&lt;script&gt;New climate caption&lt;/script&gt;', false)->assertDontSee('<script>New climate caption</script>', false);
        $this->get('/stories/students-championing-peace')->assertOk()->assertSee('Updated peace caption.');
        $this->assertSame('team', ContentSection::where('slug', 'galleries')->firstOrFail()->published['climate_5_image']);
    }

    public function test_gallery_preview_can_open_the_peace_story_without_publishing(): void
    {
        $this->actingAs(User::factory()->editor()->create());
        $payload = $this->payload('galleries', ['peace_1_alt' => 'Private gallery caption.'], 'preview');
        $payload['preview_page'] = 'peace';

        $this->put('/admin/content/galleries', $payload)->assertRedirect('/stories/students-championing-peace?preview=galleries');

        $this->get('/stories/students-championing-peace?preview=galleries')->assertOk()->assertSee('Private gallery caption.');
        $this->get('/stories/students-championing-peace')->assertOk()->assertDontSee('Private gallery caption.');
        $this->assertNull(ContentSection::where('slug', 'galleries')->firstOrFail()->published);
    }

    public function test_home_visibility_button_destinations_and_search_copy_are_editable(): void
    {
        $this->actingAs(User::factory()->editor()->create());

        $this->put('/admin/content/home', $this->payload('home', [
            'show_partners' => 'hide', 'primary_page' => 'contact', 'secondary_page' => 'about',
            'seo_title' => 'Community learning in Masaka', 'featured_story' => 'learning-through-making',
        ]))->assertRedirect();

        $this->get('/')->assertOk()->assertDontSee('id="partners-heading"', false)
            ->assertSee('<title>Community learning in Masaka | Ideal Woman</title>', false)
            ->assertSee('/stories/learning-through-making')->assertSee('class="hero-secondary" href="'.route('about').'"', false);
        $this->assertSame('contact', ContentSection::where('slug', 'home')->firstOrFail()->published['primary_page']);
    }

    public function test_unavailable_images_and_unapproved_destinations_are_rejected(): void
    {
        $this->actingAs(User::factory()->editor()->create());

        $this->put('/admin/content/home', $this->payload('home', ['hero_image_1' => 'missing-image']))->assertSessionHasErrors(['fields.hero_image_1' => 'Choose an image from the library.']);
        $this->put('/admin/content/home', $this->payload('home', ['primary_page' => 'admin.users.index']))->assertSessionHasErrors('fields.primary_page');
        $this->put('/admin/content/home', $this->payload('home', ['hero_image_1' => '']))->assertSessionHasErrors('fields.hero_image_1');
        $this->put('/admin/content/galleries', $this->payload('galleries', ['climate_1_image' => '../../.env']))->assertSessionHasErrors('fields.climate_1_image');
        $this->put('/admin/content/galleries', $this->payload('galleries', ['climate_1_alt' => '']))->assertSessionHasErrors('fields.climate_1_alt');

        $this->assertDatabaseCount('content_sections', 0);
    }

    public function test_old_saved_pages_receive_new_controls_without_erasing_their_content(): void
    {
        ContentSection::factory()->create(['slug' => 'home', 'draft' => ['text_010' => 'An earlier heading.'], 'published' => ['text_010' => 'An earlier heading.'], 'version' => 1]);
        $this->actingAs(User::factory()->editor()->create());

        $this->get('/admin/content/home')->assertOk()->assertSee('An earlier heading.')->assertSee('name="fields[hero_image_1]"', false);
        $this->put('/admin/content/home', $this->payload('home', ['text_010' => 'An earlier heading.'], 'draft', 1))->assertRedirect();

        $record = ContentSection::where('slug', 'home')->firstOrFail();
        $this->assertSame('An earlier heading.', $record->draft['text_010']);
        $this->assertSame('together', $record->draft['hero_image_1']);
        $this->assertSame(['text_010' => 'An earlier heading.'], $record->published);
    }

    public function test_story_save_and_preview_opens_the_latest_private_draft(): void
    {
        $this->actingAs(User::factory()->editor()->create());

        $this->post('/admin/stories', ['slug' => 'new-community-day', 'title' => 'New community day', 'category' => 'Community',
            'excerpt' => 'Learning together.', 'body' => 'A private draft story.', 'image' => 'team', 'alt' => 'Our team.', 'action' => 'preview', 'version' => 0])
            ->assertRedirect('/stories/new-community-day?preview=story-new-community-day');

        $record = ContentSection::where('slug', 'story-new-community-day')->firstOrFail();
        $this->assertNull($record->published);
        $this->get('/stories/new-community-day?preview=story-new-community-day')->assertOk()->assertSee('A private draft story.')
            ->assertSee(route('admin.stories.edit', $record));
        $this->get('/stories/new-community-day')->assertNotFound();
    }

    public function test_programme_photograph_changes_reach_the_home_card_and_work_page(): void
    {
        $this->actingAs(User::factory()->editor()->create());

        $this->put('/admin/content/programme-community-environment', $this->payload('programme-community-environment', ['image' => 'team', 'alt' => 'Updated programme photo.']))->assertRedirect();

        $this->get('/')->assertOk()->assertSee('alt="Updated programme photo."', false);
        $this->get('/our-work')->assertOk()->assertSee('alt="Updated programme photo."', false);
    }
}
