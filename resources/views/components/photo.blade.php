@props(['name', 'alt', 'eager' => false, 'wide' => false, 'deferred' => false, 'sizes' => '(min-width: 1024px) 50vw, 100vw'])
@php($photo = $site->photo($name))
<picture {{ $attributes->class(['photo']) }}>
    <img @if($deferred) src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="{{ $photo['src'] }}" data-srcset="{{ $photo['srcset'] }}" @else src="{{ $photo['src'] }}" @if($photo['srcset']) srcset="{{ $photo['srcset'] }}" @endif @endif sizes="{{ $sizes }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" alt="{{ $alt }}" loading="{{ $eager ? 'eager' : 'lazy' }}" @if($eager) fetchpriority="high" @endif decoding="async">
</picture>
