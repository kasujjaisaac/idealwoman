<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    public static function pages(): array
    {
        return [['/', 'Her potential.'], ['/about', 'Bagadala Elizabeth'], ['/our-work', 'Skills &amp; Livelihoods'], ['/stories', 'Students Championing Peace'], ['/get-involved', 'Send support enquiry'], ['/contact', 'Send an enquiry'], ['/stories/students-championing-peace', 'violent extremism'], ['/stories/learning-through-making', 'sewing']];
    }

    #[DataProvider('pages')]
    public function test_public_pages_render_verified_content(string $url, string $text): void
    {
        $this->get($url)->assertOk()->assertSee($text, false)->assertSee('noindex, nofollow', false)->assertDontSee('@endif', false)->assertDontSee('@if(', false);
    }

    public function test_unknown_story_returns_404(): void
    {
        $this->get('/stories/missing')->assertNotFound();
    }

    public function test_marathon_logo_uploaded_to_media_displays_without_a_public_image(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('cms/partner-masaka-marathon.png', 'logo');

        $this->get('/')->assertOk()->assertSee('/media/partner-masaka-marathon.png', false);
    }

    public function test_support_page_has_a_protected_form_without_the_previous_sections(): void
    {
        $this->get('/get-involved')->assertOk()
            ->assertSee('Send support enquiry')
            ->assertSee('name="_token"', false)
            ->assertSee('name="website"', false)
            ->assertSee('<option value="Equipment &amp; programme support" selected>Equipment &amp; programme support</option>', false)
            ->assertDontSee('Institutional partnerships')
            ->assertDontSee('involvement-card', false)
            ->assertDontSee('profile-panel', false);
    }

    public function test_sitemap_contains_all_pages_and_stories(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')->assertSee('/get-involved')->assertSee('/volunteer-with-us')->assertSee('/stories/learning-through-making');
    }

    public function test_volunteer_page_has_a_protected_form_and_is_linked_from_navigation(): void
    {
        $this->get('/volunteer-with-us')->assertOk()
            ->assertSee('Volunteer With Us')
            ->assertSee('Send volunteer enquiry')
            ->assertSee('name="_token"', false)
            ->assertSee('name="website"', false)
            ->assertSee('<option value="Volunteering" selected>Volunteering</option>', false);

        $this->get('/')->assertOk()->assertSee('/volunteer-with-us');
    }

    public function test_indexing_is_disabled_by_default_and_can_be_enabled(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
        config(['idealwoman.indexable' => true]);
        $this->get('/robots.txt')->assertOk()->assertSee('Allow: /')->assertSee('/sitemap.xml');
        $this->get('/')->assertOk()->assertSee('index, follow');
    }

    public function test_partnership_enquiry_preselects_type_and_has_csrf_token(): void
    {
        $this->get('/contact?type=Partnership')->assertOk()->assertSee('name="_token"', false)->assertSee('<option value="Partnership" selected>Partnership</option>', false);
    }
}
