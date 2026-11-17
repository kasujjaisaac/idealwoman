@props(['name', 'alt', 'eager' => false, 'wide' => false, 'deferred' => false, 'sizes' => '(min-width: 1336px) 596px, (min-width: 1024px) calc((100vw - 144px) / 2), (min-width: 601px) calc(100vw - 96px), calc(100vw - 40px)'])
@php($photo = $site->photo($name))
<picture {{ $attributes->class(['photo']) }}>
    <img @if($deferred) src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="{{ $photo['src'] }}" data-srcset="{{ $photo['srcset'] }}" @else src="{{ $photo['src'] }}" @if($photo['srcset']) srcset="{{ $photo['srcset'] }}" @endif @endif sizes="{{ $sizes }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" alt="{{ $alt }}" loading="{{ $eager ? 'eager' : 'lazy' }}" @if($eager) fetchpriority="high" @endif decoding="async">
</picture>
