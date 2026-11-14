<x-layout title="Women’s Empowerment in Masaka, Uganda">
<section class="home-slider" aria-label="Ideal Woman community photographs" aria-roledescription="carousel" tabindex="0" aria-describedby="slider-help">
    <p id="slider-help" class="sr-only">{{ $site->text('home.text_006') }}</p>
    <div class="slider-photos">
        <x-photo class="slider-slide is-active" name="together" :alt="$site->text('home.text_001')" :eager="true" :wide="true" sizes="100vw"/>
        <x-photo class="slider-slide" name="peace-school-gathering" :alt="$site->text('home.text_002')" :deferred="true" sizes="100vw" aria-hidden="true"/>
        <x-photo class="slider-slide" name="peace-club-students" :alt="$site->text('home.text_003')" :deferred="true" sizes="100vw" aria-hidden="true"/>
        <x-photo class="slider-slide" name="peace-school-club" :alt="$site->text('home.text_004')" :deferred="true" sizes="100vw" aria-hidden="true"/>
    </div>
    <div class="slider-gradient"></div>
    <div class="slider-side-controls" hidden>
        <button type="button" class="slide-side previous-slide" data-slide-step="-1" aria-label="Previous photograph"><span aria-hidden="true">&#8249;</span></button>
        <button type="button" class="slide-side next-slide" data-slide-step="1" aria-label="Next photograph"><span aria-hidden="true">&#8250;</span></button>
        <span class="sr-only slide-announcement" aria-live="off" aria-atomic="true">{{ $site->text('home.text_007') }}</span>
    </div>
    <div class="wrap slider-content">
        <p class="eyebrow light hero-eyebrow"><span>{{ $site->text('home.text_008') }}</span> <span>{{ $site->text('home.text_009') }}</span></p>
        <h1 class="hero-title"><span>{{ $site->text('home.text_010') }}</span><span>{{ $site->text('home.text_011') }}</span><em>{{ $site->text('home.text_012') }}</em></h1>
        <p class="hero-description">{{ $site->text('home.text_013') }}</p>
        <div class="hero-buttons"><a class="button white" href="{{ route('work') }}">{{ $site->text('home.text_014') }}</a><a class="hero-secondary" href="{{ route('involved') }}#partnership">{{ $site->text('home.text_015') }}</a></div>

    </div>
</section>
<section class="principles" aria-label="Our principles"><div class="wrap grid gap-6 md:grid-cols-3"><div><x-icon name="pin"/><span>{{ $site->text('home.text_016') }}</span></div><div><x-icon name="community"/><span>{{ $site->text('home.text_017') }}</span></div><div><x-icon name="opportunity"/><span>{{ $site->text('home.text_018') }}</span></div></div></section>
<section class="section wrap about-intro grid items-center gap-12 lg:grid-cols-2"><div class="image-frame"><x-photo name="team" :alt="$site->text('home.text_005')"/><span class="image-note">{{ $site->text('home.text_019') }}</span></div><div class="section-copy"><p class="eyebrow">{{ $site->text('home.text_020') }}</p><h2>{{ $site->text('home.text_021') }}<br>{{ $site->text('home.text_022') }}</h2><p class="lede">{{ $site->text('home.text_023') }}</p><p>{{ $site->text('home.text_024') }}</p><a class="button action-button" href="{{ route('about') }}">{{ $site->text('home.text_025') }}</a></div></section>
<section class="section work-section"><div class="wrap"><div class="section-heading"><div><p class="eyebrow">{{ $site->text('home.text_026') }}</p><h2>{{ $site->text('home.text_027') }}<br>{{ $site->text('home.text_028') }}</h2></div><p>{{ $site->text('home.text_029') }}<br>{{ $site->text('home.text_030') }}</p></div><x-work-cards/></div></section>
<section class="section wrap featured grid items-center gap-12 lg:grid-cols-2"><div class="section-copy"><p class="eyebrow">{{ $site->text('home.text_031') }}</p><h2>{{ $site->text('home.text_032') }}<br>{{ $site->text('home.text_033') }}</h2><p class="lede">{{ $site->text('home.text_034') }}</p><p>{{ $site->text('home.text_035') }}</p><a href="{{ (config('idealwoman.stories.students-championing-peace') ? route('stories.show', 'students-championing-peace') : route('stories')) }}" class="button action-button">{{ $site->text('home.text_036') }}</a></div><figure><x-photo name="peace-school-gathering" :alt="$site->text('home.text_002')"/></figure></section>
<section class="section partners-section" aria-labelledby="partners-heading">
    <div class="wrap">
        <div class="section-heading">
            <div><p class="eyebrow">{{ $site->text('home.text_037') }}</p><h2 id="partners-heading">{{ $site->text('home.text_038') }}</h2></div>
            <p>{{ $site->text('home.text_039') }}</p>
        </div>
        <ul class="partners-grid" role="list">
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-masaka-city', asset('images/partners/masaka-city.png')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_040') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-masaka-district', asset('images/partners/masaka-district.png')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_041') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-masaka-city-ngo-forum', asset('images/partners/masaka-city-ngo-forum.jpeg')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_042') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-masaka-district', asset('images/partners/masaka-district.png')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_043') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-rotaract', asset('images/partners/rotaract.png')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_044') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-mahipso', asset('images/partners/mahipso.png')) }}" alt="" loading="lazy" decoding="async" width="164" height="220"></div>
                <p class="partner-name">{{ $site->text('home.text_045') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-nchrd-u', asset('images/partners/nchrd-u.png')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_046') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-unaso', asset('images/partners/unaso.png')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_047') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-prospect-initiative', asset('images/partners/prospect-initiative.webp')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_048') }}</p>
            </li>
            <li class="partner-card">
                <div class="partner-logo"><img src="{{ $site->media('partner-creative-learning-center', asset('images/partners/creative-learning-center.png')) }}" alt="" loading="lazy" decoding="async" width="240" height="112"></div>
                <p class="partner-name">{{ $site->text('home.text_049') }}</p>
            </li>
        </ul>
    </div>
</section>
<x-partnership/>
</x-layout>
