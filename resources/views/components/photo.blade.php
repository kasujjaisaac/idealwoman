@props(['name', 'alt', 'eager' => false, 'wide' => false, 'sizes' => '(min-width: 1024px) 50vw, 100vw'])
@php($customImage = $site->media($name, ''))
<picture {{ $attributes->class(['photo']) }}>
    @if(! $site->hasMedia($name) && ! $customImage)
    <source type="image/webp" srcset="{{ asset('images/'.$name.'-640.webp') }} 640w, {{ asset('images/'.$name.'-960.webp') }} 960w, {{ asset('images/'.$name.'-1600.webp') }} 1600w, {{ asset('images/'.$name.'-2560.webp') }} 2560w{{ $wide ? ', '.asset('images/'.$name.'-3200.webp').' 3200w' : '' }}" sizes="{{ $sizes }}">
    @endif
    <img src="{{ $customImage ?: asset('images/'.$name.'.jpg') }}" width="1600" height="{{ in_array($name, ['workshop', 'together']) ? 1067 : ($name === 'tailoring' ? 1064 : 1063) }}" alt="{{ $alt }}" loading="{{ $eager ? 'eager' : 'lazy' }}" @if($eager) fetchpriority="high" @endif decoding="async">
</picture>
