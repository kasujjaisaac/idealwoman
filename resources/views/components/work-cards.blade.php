<div class="work-grid">
    @foreach(config('idealwoman.work') as $slug => $work)
        <a class="work-card" href="{{ route('work') }}#{{ $slug }}">
            @php
                $cardPhoto = match ($slug) {
                    'community-environment' => ['name' => $work['image'] ?: 'together', 'alt' => $work['image'] ? ($work['alt'] ?? '') : 'Workshop participants gathered behind a table with laptops.'],
                    default => ['name' => $work['image'], 'alt' => $work['alt'] ?? ''],
                };
            @endphp
            @if($cardPhoto['name'])
            <x-photo class="work-card-photo" :name="$cardPhoto['name']" :alt="$cardPhoto['alt']" sizes="(min-width: 901px) 33vw, (min-width: 601px) 50vw, 100vw"/>
            @endif
            <div class="work-card-body">
            <div class="card-top">
                <span class="work-icon-panel"><x-icon :name="$work['icon']"/></span>
            </div>
            <h3>{{ $work['title'] }}</h3>
            <p>{{ $work['description'] }}</p>
            <span class="work-card-footer"><span class="button work-card-action">{{ $site->text('work.card_button') }}</span><span class="work-card-marker" aria-hidden="true"></span></span>
            </div>
        </a>
    @endforeach
</div>
