@props(['eyebrow', 'title', 'intro', 'backUrl' => null, 'backLabel' => null])
<section class="page-heading compact-hero">
    <div class="wrap">
        <div class="page-hero-copy">
        @if($backUrl)
            <a class="hero-breadcrumb" href="{{ $backUrl }}"> {{ $backLabel }}</a>
        @else
            <a class="hero-breadcrumb" href="{{ route('home') }}">Home</a>
        @endif
        <p class="eyebrow light">{{ $eyebrow }}</p>
        <h1>{{ $title }}</h1>
        <p class="lede">{{ $intro }}</p>
        </div>
    </div>
</section>
