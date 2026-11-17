<x-admin-layout title="Overview">
@php
    $draftCount = $sections->filter(fn ($section) => $section->draft !== $section->published)->count();
@endphp
<section class="studio-welcome">
<div class="welcome-copy"><p class="studio-eyebrow">YOUR WEBSITE, AT A GLANCE</p><h1>Manage your website<span>.</span></h1><p>Keep your stories, photographs and community information up to date. Everything you need, in one place.</p><div class="welcome-actions"><a class="btn" href="{{ route('admin.stories.create') }}"><x-admin-icon name="plus"/>Write a story</a><a class="welcome-secondary" href="{{ route('admin.content.edit', 'home') }}">Edit home page <x-admin-icon name="arrow"/></a></div></div>
<div class="welcome-photo"><x-photo :name="$site->text('home.hero_image_1')" alt="" sizes="(min-width: 1024px) 360px, 100vw"/><span>Our community. Our stories.</span></div>
</section>
<div class="studio-metrics">
<a href="#website-content" class="studio-metric"><span class="metric-icon"><x-admin-icon name="file"/></span><div><p>Content sections</p><strong>{{ count($definitions) }}</strong><span>Pages, activities & shared content</span></div><x-admin-icon name="arrow" class="metric-arrow"/></a>
<a href="#website-content" class="studio-metric" data-show-drafts><span class="metric-icon metric-amber"><x-admin-icon name="clock"/></span><div><p>Unpublished changes</p><strong>{{ $draftCount }}</strong><span>{{ $draftCount ? 'Ready for your review' : 'Your saved content is up to date' }}</span></div><x-admin-icon name="arrow" class="metric-arrow"/></a>
<a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="studio-metric"><span class="metric-icon metric-green"><x-admin-icon name="mail"/></span><div><p>New enquiries</p><strong>{{ $newEnquiries }}</strong><span>Conversations waiting for a reply</span></div><x-admin-icon name="arrow" class="metric-arrow"/></a>
</div>
<section class="studio-quick-section"><div class="studio-section-heading"><div><p class="studio-eyebrow">QUICK EDITS</p><h2>What would you like to change?</h2></div><span class="section-hint">The everyday essentials</span></div><div class="studio-shortcuts">
@foreach(['home' => ['Home & hero photos', 'Update the first impression', 'home'], 'media' => ['Logo & website images', 'Photographs, branding and PDF', 'image'], 'galleries' => ['Activity galleries', 'Climate and peace photographs', 'grid'], 'settings' => ['Contact details', 'Phone, email and office map', 'mail'], 'social' => ['Social media', 'Keep your community connected', 'globe'], 'navigation' => ['Website menu', 'Change your navigation labels', 'settings']] as $key => [$label, $description, $icon])
<a class="studio-shortcut" href="{{ route('admin.content.edit', $key) }}"><span class="shortcut-icon"><x-admin-icon :name="$icon"/></span><div><h3>{{ $label }}</h3><p>{{ $description }}</p></div><x-admin-icon name="arrow" class="shortcut-arrow"/></a>
@endforeach
</div></section>
<section id="website-content" class="studio-content-section" data-content-catalog>
<div class="studio-section-heading"><div><p class="studio-eyebrow">YOUR CONTENT</p><h2>Pages & shared content</h2></div><label class="studio-search"><x-admin-icon name="search"/><input data-filter aria-label="Find a content section" placeholder="Find a page or activity"></label></div>
<div class="catalog-filters" role="group" aria-label="Filter website content">@foreach(['all' => 'All content', 'page' => 'Website pages', 'programme' => 'Activities', 'shared' => 'Shared content', 'draft' => 'Draft changes'] as $filter => $label)<button type="button" data-catalog-filter="{{ $filter }}" aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}">{{ $label }}</button>@endforeach</div>
<p data-catalog-status class="sr-only" role="status" aria-live="polite"></p><div class="studio-content-grid">
@foreach($definitions as $slug => $definition)
@php
    $category = str_starts_with($slug, 'programme-') ? 'programme' : (in_array($slug, ['home','about','work','stories','involved','contact']) ? 'page' : 'shared');
    $hasDraft = isset($sections[$slug]) && $sections[$slug]->draft !== $sections[$slug]->published;
@endphp
<a data-searchable data-category="{{ $category }}" data-has-draft="{{ $hasDraft ? 'true' : 'false' }}" class="studio-content-card" href="{{ route('admin.content.edit', $slug) }}"><div class="content-card-top"><span class="content-kind"><x-admin-icon :name="$category === 'programme' ? 'grid' : ($category === 'page' ? 'file' : 'settings')"/>{{ $category === 'programme' ? 'Activity' : ($category === 'page' ? 'Page' : 'Shared content') }}</span><x-admin-icon name="arrow"/></div><h3>{{ $definition['label'] }}</h3><p>{{ count($definition['fields']) }} editable fields</p><div class="content-card-bottom"><span @class(['badge', 'badge-draft' => $hasDraft, 'badge-live' => isset($sections[$slug]) && $sections[$slug]->published !== null && !$hasDraft])>{{ $hasDraft ? 'Draft changes' : (isset($sections[$slug]) && $sections[$slug]->published !== null ? 'Published' : 'Original content') }}</span><span>Edit content</span></div></a>
@endforeach
</div><p data-catalog-empty hidden class="catalog-empty">No content matches. Try another search or choose All content.</p>
</section>
<div class="studio-bottom-grid"><section class="panel studio-activity"><div class="studio-section-heading"><h2>Recent activity</h2><x-admin-icon name="clock"/></div><div class="activity-list">@forelse($revisions as $revision)<div class="activity-entry"><span class="activity-marker"><x-admin-icon name="{{ $revision->action === 'publish' ? 'check' : 'file' }}"/></span><div><p><strong>{{ $revision->author?->name ?? 'Former team member' }}</strong> {{ $revision->action === 'publish' ? 'published' : ($revision->action === 'restore' ? 'restored' : 'saved') }} content</p><small>Revision #{{ $revision->id }} | {{ $revision->created_at->timezone('Africa/Kampala')->format('d M Y, H:i') }}</small></div></div>@empty<div class="activity-empty"><x-admin-icon name="clock"/><p>No changes yet</p><span>Your team's saved changes will appear here.</span></div>@endforelse</div></section>
<aside class="studio-guidance"><x-admin-icon name="shield"/><p class="studio-eyebrow">A SIMPLE WORKFLOW</p><h2>Edit. Preview.<br>Publish with confidence.</h2><ol><li><span>01</span>Save your changes as a private draft.</li><li><span>02</span>Preview the page before it goes live.</li><li><span>03</span>Publish when you are ready.</li></ol><p>Every save keeps a revision, so you can restore earlier content.</p>@can('manage-users')<a href="{{ route('admin.users.index') }}">Manage administrators <x-admin-icon name="arrow"/></a>@endcan</aside></div>
</x-admin-layout>
