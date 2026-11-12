@props(['title' => 'Her potential. Her choices. Her future.', 'description' => 'Ideal Woman Community Based Organisation supports girls, young women and communities across Greater Masaka through education, practical skills and community connection.', 'image' => 'community.jpg'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $site->text('settings.tab_title') }}</title>
    <meta name="description" content="{{ $description === 'Ideal Woman Community Based Organisation supports girls, young women and communities across Greater Masaka through education, practical skills and community connection.' ? $site->text('settings.default_description') : $description }}">
    <meta name="robots" content="{{ config('idealwoman.indexable') ? 'index, follow' : 'noindex, nofollow' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="{{ request()->routeIs('stories.show') ? 'article' : 'website' }}">
    <meta property="og:title" content="{{ $title }} | Ideal Woman">
    <meta property="og:description" content="{{ $description === 'Ideal Woman Community Based Organisation supports girls, young women and communities across Greater Masaka through education, practical skills and community connection.' ? $site->text('settings.default_description') : $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $site->media(pathinfo($image, PATHINFO_FILENAME), asset('images/'.$image)) }}">
    <meta property="og:site_name" content="Ideal Woman">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#153CC6">
    <link rel="icon" type="image/png" href="{{ $site->media('logo', asset('images/logo.png')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'Ideal Woman Community Based Organisation', 'url' => config('app.url'), 'logo' => asset('images/logo.png'), 'email' => 'info@idealwomanuganda.com', 'telephone' => ['+256700947613', '+256783755103'], 'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Muto Complex, Main Floor, Room 42', 'addressLocality' => 'Masaka City', 'addressCountry' => 'UG']], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!}</script>
</head>
<body @class(['home-page' => request()->routeIs('home')])>
@if(request()->has('preview'))<div role="status" style="padding:12px;background:#fff3cd;color:#664d03;text-align:center">Private draft preview ? Changes are not published.</div>@endif
<a href="#main" class="skip-link">{{ $site->text('shared.text_001') }}</a>
<div class="topline" aria-label="Contact and support">
    <div class="wrap topbar-inner">
        <div class="topbar-contacts">
            <a class="topbar-location" href="{{ route('contact') }}"><x-icon name="pin"/>{{ $site->text('shared.text_002') }}</a>
            <a href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.phone')) }}"><x-icon name="phone"/>{{ $site->text('settings.phone') }}</a>
            <a class="topbar-second-phone" href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.secondary_phone')) }}"><x-icon name="phone"/>{{ $site->text('settings.secondary_phone') }}</a>
            <a class="topbar-email" href="mailto:{{ preg_replace('/\s+/', '', $site->text('settings.email')) }}"><x-icon name="email"/>{{ $site->text('settings.email') }}</a>
        </div>
        <div class="topbar-actions">
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
<main id="main">{{ $slot }}</main>
<footer class="footer">
    <div class="wrap footer-grid">
        <div><a class="footer-brand" href="{{ route('home') }}"><img src="{{ $site->media('logo', asset('images/logo.png')) }}" width="900" height="266" alt="Ideal Woman. Reaching out to you." loading="lazy"></a><p class="footer-name">{{ $site->text('shared.text_006') }}</p><p>{{ $site->text('shared.text_007') }}</p><p class="footer-note">{{ $site->text('shared.text_008') }}<br>{{ $site->text('shared.text_009') }}</p><x-social-links label="Follow Ideal Woman on social media"/></div>
        <div><h2>{{ $site->text('shared.text_010') }}</h2><ul>@foreach(['about' => 'About Us', 'work' => 'Our Work', 'stories' => 'Stories & Updates', 'involved' => 'Get Involved', 'contact' => 'Contact'] as $route => $label)<li><a href="{{ route($route) }}">{{ $site->text('navigation.'.$route) }}</a></li>@endforeach<li><a href="{{ $site->media('profile', asset('downloads/ideal-woman-organisational-profile.pdf')) }}" download="Ideal-Woman-Organisational-Profile.pdf">{{ $site->text('shared.text_011') }}</a></li></ul></div>
        <div><h2>{{ $site->text('shared.text_012') }}</h2><address>{{ $site->text('shared.text_013') }}<br>{{ $site->text('shared.text_014') }}<br><br>{{ $site->text('shared.text_015') }}</address><a class="footer-map" href="{{ $site->text('settings.map_url') }}">{{ $site->text('shared.text_016') }}</a></div>
        <div><h2>{{ $site->text('shared.text_017') }}</h2><ul><li><a href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.phone')) }}">{{ $site->text('settings.phone') }}</a></li><li><a href="tel:{{ preg_replace('/\s+/', '', $site->text('settings.secondary_phone')) }}">{{ $site->text('settings.secondary_phone') }}</a></li><li><a href="mailto:{{ preg_replace('/\s+/', '', $site->text('settings.email')) }}">{{ $site->text('settings.email') }}</a></li><li><a href="mailto:{{ preg_replace('/\s+/', '', $site->text('settings.secondary_email')) }}">{{ $site->text('settings.secondary_email') }}</a></li></ul></div>
    </div>
    <div class="wrap footer-bottom"><span>&copy; {{ now()->year }} Ideal Woman CBO. All rights reserved.</span><span>{{ $site->text('shared.text_002') }} <span aria-hidden="true">&#183;</span> {{ $site->text('shared.text_007') }}</span></div>
</footer>
</body>
</html>
