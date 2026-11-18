@props(['title' => 'Her potential. Her choices. Her future.', 'description' => 'Ideal Woman Community Based Organisation supports girls, young women and communities across Greater Masaka through education, practical skills and community connection.', 'image' => 'community.jpg'])
@php
    $pageTitle = $title.' | '.$site->text('settings.tab_title');
    $pageDescription = $description === 'Ideal Woman Community Based Organisation supports girls, young women and communities across Greater Masaka through education, practical skills and community connection.' ? $site->text('settings.default_description') : $description;
    $indexable = config('idealwoman.indexable') && ! request()->has('preview');
    $socialProfiles = array_values(array_filter(config('idealwoman.social_links', []), fn ($url) => is_string($url) && filter_var($url, FILTER_VALIDATE_URL) && str_starts_with($url, 'https://')));
    $organisationId = rtrim(config('app.url'), '/').'#organisation';
    $websiteId = rtrim(config('app.url'), '/').'#website';
    $pageUrl = url()->current();
    $pageImage = $site->media(pathinfo($image, PATHINFO_FILENAME), asset('images/'.$image));
    $breadcrumbs = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')]];
    if (request()->routeIs('stories.show')) {
        $breadcrumbs[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $site->text('navigation.stories'), 'item' => route('stories')];
    }
    if (! request()->routeIs('home')) {
        $breadcrumbs[] = ['@type' => 'ListItem', 'position' => count($breadcrumbs) + 1, 'name' => $title, 'item' => $pageUrl];
    }
    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'Organization', '@id' => $organisationId, 'name' => 'Ideal Woman Community Based Organisation', 'url' => config('app.url'), 'logo' => $site->media('logo', asset('images/logo.png')), 'email' => $site->text('settings.email'), 'telephone' => [$site->text('settings.phone'), $site->text('settings.secondary_phone')], 'sameAs' => $socialProfiles, 'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Muto Complex, Main Floor, Room 42', 'addressLocality' => 'Masaka City', 'addressCountry' => 'UG']],
            ['@type' => request()->routeIs('stories.show') ? 'Article' : 'WebPage', '@id' => $pageUrl.'#webpage', 'name' => $title, 'headline' => $title, 'description' => $pageDescription, 'url' => $pageUrl, 'image' => $pageImage, 'inLanguage' => 'en', 'isPartOf' => ['@id' => $websiteId], 'mainEntityOfPage' => $pageUrl, 'author' => ['@id' => $organisationId], 'publisher' => ['@id' => $organisationId]],
            ['@type' => 'WebSite', '@id' => $websiteId, 'name' => 'Ideal Woman', 'url' => route('home'), 'inLanguage' => 'en', 'publisher' => ['@id' => $organisationId]],
        ],
    ];
    if (! request()->routeIs('home')) {
        $structuredData['@graph'][] = ['@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumbs];
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ $indexable ? 'index, follow, max-image-preview:large' : 'noindex, nofollow' }}">
    @if(config('idealwoman.google_site_verification'))
    <meta name="google-site-verification" content="{{ config('idealwoman.google_site_verification') }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="{{ request()->routeIs('stories.show') ? 'article' : 'website' }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:alt" content="{{ $title }} — Ideal Woman community activities in Uganda">
    <meta property="og:locale" content="en_UG">
    <meta property="og:site_name" content="Ideal Woman">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">
    <meta name="twitter:image:alt" content="{{ $title }} — Ideal Woman community activities in Uganda">
    <meta name="theme-color" content="#153CC6">
    <link rel="icon" type="image/png" href="{{ $site->media('logo', asset('images/logo.png')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) !!}</script>
</head>
<body @class(['home-page' => request()->routeIs('home')])>
@if(request()->has('preview'))<div role="status" style="padding:16px;background:#fff3cd;color:#664d03;text-align:center">Private draft preview. Changes are not published. <a href="{{ $site->editorUrl((string) request('preview')) }}" style="font-weight:600;text-decoration:underline">Return to editor</a></div>@endif
@auth
@can('manage-content')
@unless(request()->has('preview'))
@php($editingSection = request()->routeIs('stories.show') ? 'story-'.request()->route('slug') : request()->route()->getName())
<div class="editor-toolbar"><span>Website editor</span><a href="{{ $site->editorUrl($editingSection) }}">Edit this page</a><a href="{{ route('admin.content.edit', 'media') }}">Website images</a><a href="{{ route('admin.dashboard') }}">Admin dashboard</a></div>
@endunless
@endcan
@endauth
<a href="#main" class="skip-link">{{ $site->text('shared.text_001') }}</a>
<x-accessibility-tools/>
<div class="topline" aria-label="Contact and support">
    <div class="wrap topbar-inner">
        <div class="topbar-contacts">
            <a class="topbar-location" href="{{ route('contact') }}"><x-icon name="pin"/>{{ $site->text('shared.text_002') }}</a>
            <a href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.phone')) }}"><x-icon name="phone"/>{{ $site->text('settings.phone') }}</a>
            <a class="topbar-second-phone" href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.secondary_phone')) }}"><x-icon name="phone"/>{{ $site->text('settings.secondary_phone') }}</a>
            <a class="topbar-email" href="mailto:{{ preg_replace('/\s+/', '', $site->text('settings.email')) }}"><x-icon name="email"/>{{ $site->text('settings.email') }}</a>
        </div>
        <div class="topbar-actions">
            <x-social-links label="Follow Ideal Woman on social media"/>
            <a class="support-button" href="{{ route('involved') }}#partnership">{{ $site->text('shared.text_003') }}</a>
        </div>
    </div>
</div>
<header class="site-header">
    <div class="wrap header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Ideal Woman home"><img src="{{ $site->media('logo', asset('images/logo.png')) }}" width="900" height="266" alt="Ideal Woman. Reaching out to you."></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation" hidden><span>{{ $site->text('shared.text_004') }}</span><span class="menu-lines" aria-hidden="true"></span></button>
        <nav id="main-navigation" aria-label="Main navigation">
            @foreach(['home' => 'Home', 'about' => 'About Us', 'work' => 'Our Work', 'stories' => 'Stories', 'involved' => 'Get Involved', 'contact' => 'Contact'] as $route => $label)
                <a href="{{ route($route) }}" @if(request()->routeIs($route, $route.'.*')) aria-current="page" @endif>{{ $site->text('navigation.'.$route) }}</a>
            @endforeach
            <a href="{{ route('involved') }}#partnership" class="button small">{{ $site->text('shared.text_005') }}</a>
        </nav>
    </div>
</header>
<main id="main" tabindex="-1">{{ $slot }}</main>
<footer class="footer">
    <div class="wrap footer-grid">
        <div><a class="footer-brand" href="{{ route('home') }}"><img src="{{ $site->media('logo', asset('images/logo.png')) }}" width="900" height="266" alt="Ideal Woman. Reaching out to you." loading="lazy"></a><p class="footer-name">{{ $site->text('shared.text_006') }}</p><p>{{ $site->text('shared.text_007') }}</p><p class="footer-note">{{ $site->text('shared.text_008') }}<br>{{ $site->text('shared.text_009') }}</p><x-social-links label="Follow Ideal Woman on social media"/></div>
        <div><h2>{{ $site->text('shared.text_010') }}</h2><ul>@foreach(['about' => 'About Us', 'work' => 'Our Work', 'stories' => 'Stories & Updates', 'involved' => 'Get Involved', 'contact' => 'Contact'] as $route => $label)<li><a href="{{ route($route) }}">{{ $site->text('navigation.'.$route) }}</a></li>@endforeach<li><a href="{{ $site->media('profile', asset('downloads/ideal-woman-organisational-profile.pdf')) }}" download="Ideal-Woman-Organisational-Profile.pdf">{{ $site->text('shared.text_011') }}</a></li></ul></div>
        <div><h2>{{ $site->text('shared.text_012') }}</h2><address class="footer-address"><div class="footer-address-row"><x-icon name="pin"/><span>{{ $site->text('shared.text_013') }}<br>{{ $site->text('shared.text_014') }}</span></div><div class="footer-address-row"><x-icon name="email"/><span>{{ $site->text('shared.text_015') }}</span></div></address><a class="footer-map" href="{{ $site->text('settings.map_url') }}"><x-icon name="map"/><span>{{ $site->text('shared.text_016') }}</span></a></div>
        <div><h2>{{ $site->text('shared.text_017') }}</h2><ul class="footer-contacts"><li><a href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.phone')) }}"><x-icon name="phone"/><span>{{ $site->text('settings.phone') }}</span></a></li><li><a href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.secondary_phone')) }}"><x-icon name="phone"/><span>{{ $site->text('settings.secondary_phone') }}</span></a></li><li><a href="mailto:{{ preg_replace('/\s+/', '', $site->text('settings.email')) }}"><x-icon name="email"/><span>{{ $site->text('settings.email') }}</span></a></li>@if($site->text('settings.secondary_email') !== '' && $site->text('settings.secondary_email') !== $site->text('settings.email'))<li><a href="mailto:{{ preg_replace('/\s+/', '', $site->text('settings.secondary_email')) }}"><x-icon name="email"/><span>{{ $site->text('settings.secondary_email') }}</span></a></li>@endif</ul></div>
    </div>
    <div class="wrap footer-bottom"><span>&copy; {{ now()->year }} Ideal Woman CBO. All rights reserved.</span><span>{{ $site->text('shared.text_002') }} <span aria-hidden="true">&#183;</span> {{ $site->text('shared.text_007') }}</span></div>
</footer>
</body>
</html>
