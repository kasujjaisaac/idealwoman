<x-layout :title="$site->text('work.text_001')" :description="$site->text('work.text_002')" :image="$site->text('work.seo_image').'.jpg'">
<x-page-heading :eyebrow="$site->text('work.text_003')" :title="$site->text('work.text_004')" :intro="$site->text('work.text_005')"/>
<section class="wrap work-navigation"><p>{{ $site->text('work.text_006') }}</p><nav aria-label="Work areas">@foreach(config('idealwoman.work') as $slug => $work)<a href="#{{ $slug }}">{{ $work['title'] }}</a>@endforeach</nav></section>
@foreach(config('idealwoman.work') as $slug => $work)
<section id="{{ $slug }}" class="section work-detail {{ $loop->even ? 'work-section' : '' }}"><div class="wrap grid items-center gap-12 lg:grid-cols-2"><div><p class="eyebrow">{{ $site->text('work.objectives_label') }} / 0{{ $loop->iteration }}</p><x-icon :name="$work['icon']" class="work-icon"/><h2>{{ $work['title'] }}</h2><p class="lede">{{ $work['intro'] }}</p><p>{{ $work['detail'] }}</p><ul class="profile-objectives">@foreach(config('idealwoman.objectives.'.$slug, []) as $objective)<li>{{ $objective }}</li>@endforeach</ul><a class="button action-button" href="{{ route('contact', ['type' => 'General enquiry']) }}">{{ $site->text('work.text_008') }}</a></div>@if($work['image'])<x-photo :name="$work['image']" :alt="$work['alt']"/>@else<div class="objective-panel"><x-icon :name="$work['icon']"/><p>{{ $work['description'] }}</p><span>{{ $site->text('work.text_009') }}</span></div>@endif</div></section>
@if($slug === 'climate-change')
    <div class="wrap activity-gallery grid items-start gap-8 pb-12 md:grid-cols-2" aria-label="Climate change activity photos">
        @foreach($site->gallery('climate') as $photo)
            <figure>
                <x-photo :name="$photo['name']" :alt="$photo['alt']" sizes="(min-width: 1280px) 584px, (min-width: 768px) 50vw, 100vw"/>
                <figcaption>{{ $photo['alt'] }}</figcaption>
            </figure>
        @endforeach
    </div>
@endif
@endforeach
<section class="section wrap" aria-labelledby="work-activities-heading">
    <div class="section-heading">
        <div><p class="eyebrow">{{ $site->text('work.text_010') }}</p><h2 id="work-activities-heading">{{ $site->text('work.text_011') }}</h2></div>
        <p>{{ $site->text('work.text_012') }}</p>
    </div>
    <div class="grid gap-10 md:grid-cols-2">
        @foreach(config('idealwoman.stories') as $slug => $activity)
            <article class="story-card">
                <x-photo :name="$activity['image']" :alt="$activity['alt']" sizes="(min-width: 768px) 50vw, 100vw"/>
                <div class="story-copy">
                    <p class="eyebrow">{{ $activity['category'] }}</p>
                    <h2><a href="{{ route('stories.show', $slug) }}">{{ $activity['title'] }}</a></h2>
                    <p>{{ $activity['excerpt'] }}</p>
                    <a class="button action-button" href="{{ route('stories.show', $slug) }}">{{ $site->text('work.text_013') }}<span class="sr-only">: {{ $activity['title'] }}</span></a>
                </div>
            </article>
        @endforeach
    </div>
</section>
<section class="section work-section"><div class="wrap"><p class="eyebrow">{{ $site->text('work.text_014') }}</p><h2>{{ $site->text('work.text_015') }}<br>{{ $site->text('work.text_016') }}</h2><div class="involvement-grid">
    <article class="profile-panel"><h3>{{ $site->text('work.text_017') }}</h3><p>{{ $site->text('work.text_018') }}</p></article>
    <article class="profile-panel"><h3>{{ $site->text('work.text_019') }}</h3><p>{{ $site->text('work.text_020') }}</p></article>
    <article class="profile-panel"><h3>{{ $site->text('work.text_021') }}</h3><p>{{ $site->text('work.text_022') }}</p></article>
</div><div class="notice"><strong>{{ $site->text('work.text_023') }}</strong> {{ $site->text('work.text_024') }}</div></div></section>
<x-partnership/>
</x-layout>
