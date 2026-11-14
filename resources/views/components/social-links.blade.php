@props(['label' => 'Follow Ideal Woman'])
@php
    $socials = config('idealwoman.social_links', []);
    $names = ['facebook' => 'Facebook', 'instagram' => 'Instagram', 'threads' => 'Threads', 'x' => 'X', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'];
@endphp
@if(count($socials))
<div class="social-links" role="group" aria-label="{{ $label }}">
    @foreach($socials as $platform => $url)
        @php($linked = is_string($url) && filter_var($url, FILTER_VALIDATE_URL) && str_starts_with($url, 'https://'))
        @if($linked)
            <a class="social-link" href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="Ideal Woman on {{ $names[$platform] }} (opens in a new tab)" title="{{ $names[$platform] }}">
        @else
            <span class="social-link social-placeholder" role="img" aria-label="{{ $names[$platform] }} profile link coming soon" title="{{ $names[$platform] }}: link coming soon">
        @endif
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                @switch($platform)
                    @case('facebook')<path d="M14 21v-8h3l.5-4H14V7c0-1 .4-2 2-2h2V1.5A22 22 0 0 0 15 1c-3 0-5 2-5 5v3H7v4h3v8"/>@break
                    @case('instagram')<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/>@break
                    @case('threads')<path d="M20 7c-1.2-3.3-3.8-5-8-5C5.5 2 3 6.5 3 12s2.5 10 9 10c5 0 8-2.5 8-6 0-3.8-3.5-6-7.5-6-3 0-4.5 1.5-4.5 3.5S9.5 17 12 17c3.5 0 5-2.5 5-6 0-3-1.5-5-4.5-5-1.5 0-3 .5-4 2"/>@break
                    @case('x')<path d="m4 3 16 18h-5L3 3h5l13 18M20 3 4 21"/>@break
                    @case('linkedin')<rect x="3" y="3" width="18" height="18" rx="1"/><path d="M7 10v7M11 17v-7h3v1c2-2 4-1 4 2v4M14 11v6"/><circle cx="7" cy="7" r=".8" fill="currentColor" stroke="none"/>@break
                    @case('youtube')<path d="M21 7c-.3-1-1-2-2-2-4-.4-10-.4-14 0-1 0-1.7 1-2 2-.5 3-.5 7 0 10 .3 1 1 2 2 2 4 .4 10 .4 14 0 1 0 1.7-1 2-2 .5-3 .5-7 0-10Z"/><path d="m10 8 6 4-6 4Z"/>@break
                    @case('tiktok')<path d="M14 3h3c.3 3 2 4.5 4 5v3c-2 0-3-.5-4-1.5V16a6 6 0 1 1-7-6v3a3 3 0 1 0 4 3Z"/>@break
                @endswitch
            </svg>
        @if($linked)</a>@else</span>@endif
    @endforeach
</div>
@endif
