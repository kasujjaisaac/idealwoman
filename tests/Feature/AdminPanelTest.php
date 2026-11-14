<?php

namespace Tests\Feature;

use App\Models\ContentSection;
use App\Models\Enquiry;
use App\Models\User;
use App\Services\SiteContent;
use Database\Seeders\ContentSectionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    private function contentPayload(string $section, array $changes = [], string $action = 'publish', int $version = 0): array
    {
        return ['fields' => array_replace((new SiteContent)->defaults($section), $changes), 'action' => $action, 'version' => $version];
    }

    private function storyPayload(array $changes = []): array
    {
        return array_replace(['slug' => 'community-learning', 'title' => 'Community learning', 'category' => 'Education', 'excerpt' => 'Learning together in Masaka.', 'body' => "First paragraph.\n\nSecond paragraph.", 'image' => 'team', 'alt' => 'Our community team.', 'action' => 'publish', 'version' => 0], $changes);
    }

    public function test_guests_are_redirected_from_admin_reads_and_writes(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->put('/admin/content/home', [])->assertRedirect('/admin/login');
        $this->post('/admin/media', [])->assertRedirect('/admin/login');
    }

    public static function roles(): array
    {
        return ['administrator' => ['admin', true, true], 'editor' => ['editor', true, false], 'ordinary user' => ['user', false, false]];
    }

    #[DataProvider('roles')]
    public function test_permissions_match_team_roles(string $role, bool $content, bool $users): void
    {
        $user = User::factory()->make(['role' => $role]);

        $this->assertSame($content, Gate::forUser($user)->allows('manage-content'));
        $this->assertSame($users, Gate::forUser($user)->allows('manage-users'));
    }

    public function test_ordinary_users_cannot_access_content(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->put('/admin/content/home', $this->contentPayload('home'))->assertForbidden();
        $this->assertDatabaseCount('content_sections', 0);
    }

    public function test_editors_cannot_manage_team_access(): void
    {
        $this->actingAs(User::factory()->editor()->create())->post('/admin/users', [])->assertForbidden();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_valid_admin_credentials_sign_in_and_logout(): void
    {
        $user = User::factory()->admin()->create();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_invalid_credentials_and_unprivileged_accounts_cannot_sign_in(): void
    {
        $user = User::factory()->create();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_sixth_login_attempt_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/admin/login', ['email' => 'admin@example.org', 'password' => 'wrong'])->assertRedirect();
        }
        $this->post('/admin/login', ['email' => 'admin@example.org', 'password' => 'wrong'])->assertTooManyRequests();
    }

    public function test_admin_pages_and_every_content_editor_render(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->seed(ContentSectionSeeder::class);

        foreach (['/admin', '/admin/stories', '/admin/stories/create', '/admin/media', '/admin/enquiries', '/admin/users', '/admin/account'] as $url) {
            $this->get($url)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }
        foreach (array_keys((new SiteContent)->definitions()) as $slug) {
            $this->get('/admin/content/'.$slug)->assertOk()->assertSee('Publish changes');
        }
        $this->get('/admin/stories/'.ContentSection::first()->id.'/edit')->assertOk();
    }

    public function test_saved_drafts_are_private_and_can_be_previewed_by_editors(): void
    {
        $user = User::factory()->editor()->create();
        $this->actingAs($user)->put('/admin/content/home', $this->contentPayload('home', ['text_010' => 'A private future.'], 'draft'))->assertRedirect()->assertSessionHas('success');
        $section = ContentSection::where('slug', 'home')->firstOrFail();
        $this->assertSame('A private future.', $section->draft['text_010']);
        $this->assertNull($section->published);
        $this->assertDatabaseHas('content_revisions', ['content_section_id' => $section->id, 'user_id' => $user->id, 'action' => 'draft']);
        $this->get('/?preview=home')->assertOk()->assertSee('A private future.')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->post('/admin/logout');
        $this->get('/')->assertOk()->assertDontSee('A private future.');
        $this->get('/?preview=home')->assertForbidden();
    }

    public function test_published_copy_is_live_and_html_is_escaped(): void
    {
        $this->actingAs(User::factory()->editor()->create())->put('/admin/content/home', $this->contentPayload('home', ['text_010' => '<script>alert(1)</script>']))->assertRedirect();

        $this->get('/')->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->assertDatabaseHas('content_sections', ['slug' => 'home', 'version' => 1]);
    }

    public function test_stale_edit_cannot_overwrite_another_save(): void
    {
        $this->actingAs(User::factory()->admin()->create())->put('/admin/content/home', $this->contentPayload('home', ['text_010' => 'First save.']))->assertRedirect();

        $this->put('/admin/content/home', $this->contentPayload('home', ['text_010' => 'Stale save.']))->assertSessionHasErrors('version');
        $this->assertSame('First save.', ContentSection::where('slug', 'home')->firstOrFail()->draft['text_010']);
        $this->assertDatabaseCount('content_revisions', 1);
    }

    public function test_unexpected_fields_and_unsafe_social_urls_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create())->put('/admin/content/home', $this->contentPayload('home', ['is_admin' => '1']))->assertSessionHasErrors('fields');
        $this->put('/admin/content/social', $this->contentPayload('social', ['facebook' => 'javascript:alert(1)']))->assertSessionHasErrors('fields.facebook');
        $this->assertDatabaseCount('content_sections', 0);
    }

    public function test_revision_restore_changes_draft_and_preserves_live_content(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->put('/admin/content/home', $this->contentPayload('home', ['text_010' => 'Earlier text.']));
        $section = ContentSection::where('slug', 'home')->firstOrFail();
        $revision = $section->revisions()->firstOrFail();
        $this->put('/admin/content/home', $this->contentPayload('home', ['text_010' => 'Current text.'], 'publish', 1));

        $this->post('/admin/content/home/restore/'.$revision->id, ['version' => 2])->assertRedirect();
        $section->refresh();
        $this->assertSame('Earlier text.', $section->draft['text_010']);
        $this->assertSame('Current text.', $section->published['text_010']);
        $this->assertDatabaseHas('content_revisions', ['content_section_id' => $section->id, 'action' => 'restore']);
    }

    public function test_revision_from_another_section_cannot_be_restored(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->put('/admin/content/home', $this->contentPayload('home'));
        $this->put('/admin/content/about', $this->contentPayload('about'));
        $revision = ContentSection::where('slug', 'about')->firstOrFail()->revisions()->firstOrFail();

        $this->post('/admin/content/home/restore/'.$revision->id, ['version' => 1])->assertNotFound();
        $this->assertDatabaseCount('content_revisions', 2);
    }

    public function test_programme_leadership_navigation_and_contact_changes_reach_public_pages(): void
    {
        $this->actingAs(User::factory()->editor()->create());
        $this->put('/admin/content/programme-skills-livelihoods', $this->contentPayload('programme-skills-livelihoods', ['title' => 'Future skills', 'objectives' => "Learn together\nBuild independence"]))->assertRedirect();
        $this->put('/admin/content/leadership', $this->contentPayload('leadership', ['leader_0_name' => 'New Director']))->assertRedirect();
        $this->put('/admin/content/settings', $this->contentPayload('settings', ['email' => 'hello@example.org', 'tab_title' => 'Ideal Woman Community']))->assertRedirect();

        $this->get('/our-work')->assertOk()->assertSee('Future skills')->assertSee('Build independence');
        $this->get('/about')->assertOk()->assertSee('New Director');
        $this->get('/contact')->assertOk()->assertSee('mailto:hello@example.org', false)->assertSee('<title>Contact | Ideal Woman Community</title>', false);
    }

    public function test_story_publishing_and_unpublishing_update_public_pages_and_sitemap(): void
    {
        $this->actingAs(User::factory()->editor()->create())->post('/admin/stories', $this->storyPayload())->assertRedirect();
        $story = ContentSection::where('slug', 'story-community-learning')->firstOrFail();
        $this->assertSame('Community learning', $story->published['title']);
        $this->get('/stories/community-learning')->assertOk()->assertSee('Second paragraph.');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/stories/community-learning');

        $this->post('/admin/stories/'.$story->id.'/unpublish', ['version' => 1])->assertRedirect();
        $this->get('/stories/community-learning')->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/stories/community-learning');
        $this->assertSame('Community learning', $story->fresh()->draft['title']);
    }

    public function test_story_drafts_can_be_previewed_without_publishing(): void
    {
        $this->actingAs(User::factory()->editor()->create())->post('/admin/stories', $this->storyPayload(['action' => 'draft']))->assertRedirect();

        $this->get('/stories/community-learning')->assertNotFound();
        $this->get('/stories/community-learning?preview=story-community-learning')->assertOk()->assertSee('First paragraph.');
        $this->assertNull(ContentSection::where('slug', 'story-community-learning')->firstOrFail()->published);
    }

    public function test_story_validation_rejects_missing_content_and_invalid_image(): void
    {
        $this->actingAs(User::factory()->editor()->create())->post('/admin/stories', [])->assertSessionHasErrors(['slug', 'title', 'category', 'excerpt', 'body', 'image', 'alt']);
        $this->post('/admin/stories', $this->storyPayload(['slug' => '../admin']))->assertSessionHasErrors('slug');
        $this->post('/admin/stories', $this->storyPayload(['image' => 'missing']))->assertSessionHasErrors('image');
        $this->assertDatabaseCount('content_sections', 0);
    }

    public function test_duplicate_story_url_and_non_story_record_are_rejected(): void
    {
        $this->actingAs(User::factory()->editor()->create())->post('/admin/stories', $this->storyPayload());
        $this->post('/admin/stories', $this->storyPayload())->assertSessionHasErrors('slug');
        $page = ContentSection::factory()->create();
        $this->put('/admin/stories/'.$page->id, $this->storyPayload())->assertNotFound();
        $this->assertDatabaseCount('content_sections', 2);
    }

    public function test_uploaded_image_can_be_assigned_and_served_without_public_storage_link(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('community.png', 1200, 800);
        $this->actingAs(User::factory()->editor()->create())->post('/admin/media', ['file' => $file])->assertRedirect()->assertSessionHas('uploaded');
        $path = Storage::disk('local')->files('cms')[0];
        Storage::disk('local')->assertExists($path);

        $this->put('/admin/content/media', $this->contentPayload('media', ['team' => $path]))->assertRedirect();
        $this->get('/about')->assertOk()->assertSee('/media/'.basename($path));
        $this->get('/media/'.basename($path))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_executable_uploads_and_file_traversal_are_rejected(): void
    {
        Storage::fake('local');
        $this->actingAs(User::factory()->editor()->create())->post('/admin/media', ['file' => UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;')])->assertSessionHasErrors('file');
        $this->get('/media/secret.php')->assertNotFound();
        $this->put('/admin/content/media', $this->contentPayload('media', ['logo' => '../.env']))->assertSessionHasErrors('fields.logo');
        Storage::disk('local')->assertDirectoryEmpty('cms');
    }

    public function test_admin_can_add_editor_and_editor_cannot_escalate_access(): void
    {
        $this->actingAs(User::factory()->admin()->create())->post('/admin/users', ['name' => 'Content Editor', 'email' => 'editor@example.org', 'password' => 'a-long-secret-password', 'password_confirmation' => 'a-long-secret-password', 'role' => 'editor'])->assertRedirect();
        $user = User::where('email', 'editor@example.org')->firstOrFail();
        $this->assertSame('editor', $user->role);
        $this->assertTrue(Hash::check('a-long-secret-password', $user->password));
        $this->actingAs($user)->patch('/admin/users/'.$user->id, ['role' => 'admin'])->assertForbidden();
        $this->assertSame('editor', $user->fresh()->role);
    }

    public function test_admin_cannot_remove_their_own_access(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user)->patch('/admin/users/'.$user->id, ['role' => 'user'])->assertUnprocessable();
        $this->assertSame('admin', $user->fresh()->role);
    }

    public function test_password_change_requires_current_password_and_hashes_new_password(): void
    {
        $user = User::factory()->editor()->create();
        $payload = ['current_password' => 'wrong', 'password' => 'a-new-long-password', 'password_confirmation' => 'a-new-long-password'];
        $this->actingAs($user)->put('/admin/account/password', $payload)->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));

        $payload['current_password'] = 'password';
        $this->put('/admin/account/password', $payload)->assertRedirect();
        $this->assertTrue(Hash::check('a-new-long-password', $user->fresh()->password));
    }

    public function test_enquiry_messages_are_escaped_and_status_updates_are_persisted(): void
    {
        $enquiry = Enquiry::factory()->create(['message' => '<script>alert(1)</script>']);
        $this->actingAs(User::factory()->editor()->create())->get('/admin/enquiries')->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);

        $this->patch('/admin/enquiries/'.$enquiry->id, ['status' => 'closed'])->assertRedirect();
        $this->assertSame('closed', $enquiry->fresh()->status);
        $this->get('/admin/enquiries?status=new')->assertDontSee('&lt;script&gt;', false);
    }

    public function test_profile_updates_require_current_password_and_preserve_role(): void
    {
        $user = User::factory()->editor()->create();
        $this->actingAs($user)->put('/admin/account/profile', ['name' => 'New Name', 'email' => 'new@example.org', 'current_password' => 'wrong'])->assertSessionHasErrors('current_password');
        $this->put('/admin/account/profile', ['name' => 'New Name', 'email' => 'new@example.org', 'current_password' => 'password', 'role' => 'admin'])->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name', 'email' => 'new@example.org', 'role' => 'editor']);
    }

    public function test_only_admins_can_reset_another_members_password(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->editor()->create();
        $payload = ['current_password' => 'password', 'password' => 'a-reset-long-password', 'password_confirmation' => 'a-reset-long-password'];
        $this->actingAs($member)->put('/admin/users/'.$admin->id.'/password', $payload)->assertForbidden();
        $this->actingAs($admin)->put('/admin/users/'.$member->id.'/password', array_replace($payload, ['current_password' => 'wrong']))->assertSessionHasErrors('current_password');
        $this->put('/admin/users/'.$member->id.'/password', $payload)->assertRedirect();

        $this->assertTrue(Hash::check('a-reset-long-password', $member->fresh()->password));
        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }

    public function test_images_can_be_added_to_programmes_without_an_original_photo(): void
    {
        $this->actingAs(User::factory()->editor()->create())->put('/admin/content/programme-community-environment', $this->contentPayload('programme-community-environment', ['image' => 'team', 'alt' => 'A community learning session.']))->assertRedirect();

        $this->get('/our-work')->assertOk()->assertSee('alt="A community learning session."', false);
    }

    public function test_incomplete_editor_payload_cannot_erase_other_fields(): void
    {
        $this->actingAs(User::factory()->editor()->create())->put('/admin/content/home', ['fields' => ['text_010' => 'A partial request.'], 'action' => 'publish', 'version' => 0])->assertSessionHasErrors('fields.text_001');

        $this->assertDatabaseCount('content_sections', 0);
    }

    public function test_command_creates_an_admin_with_a_generated_password(): void
    {
        $this->artisan('admin:create', ['email' => 'first-admin@example.org', '--name' => 'First Admin', '--generate-password' => true, '--no-interaction' => true])->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'first-admin@example.org', 'name' => 'First Admin', 'role' => 'admin']);
    }

    public function test_default_seeding_imports_stories_without_creating_accounts_or_overwriting_edits(): void
    {
        $this->seed();
        $story = ContentSection::where('slug', 'story-students-championing-peace')->firstOrFail();
        $story->update(['draft' => array_replace($story->draft, ['title' => 'An edited story'])]);

        $this->seed();

        $this->assertSame('An edited story', $story->fresh()->draft['title']);
        $this->assertDatabaseCount('content_sections', 2);
        $this->assertDatabaseCount('users', 0);
    }
}
