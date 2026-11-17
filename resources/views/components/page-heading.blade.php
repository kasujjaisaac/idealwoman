@props(['eyebrow', 'title', 'intro', 'backUrl' => null, 'backLabel' => null])
<section class="page-heading compact-hero">
    <div class="wrap">
        <div class="page-hero-copy">
        <nav class="page-breadcrumbs" aria-label="Breadcrumb">
            <a class="hero-breadcrumb" href="{{ route('home') }}">Home</a>
            @if($backUrl && $backUrl !== route('home'))
                <span aria-hidden="true">/</span><a class="hero-breadcrumb" href="{{ $backUrl }}">{{ $backLabel }}</a>
            @endif
            <span aria-hidden="true">/</span><span aria-current="page">{{ $title }}</span>
        </nav>
        <p class="eyebrow light">{{ $eyebrow }}</p>
        <h1>{{ $title }}</h1>
        <p class="lede">{{ $intro }}</p>
        </div>
    </div>
</section>
