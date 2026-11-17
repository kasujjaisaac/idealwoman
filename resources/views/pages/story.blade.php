<x-layout :title="$story['title']" :description="$story['excerpt']" :image="$story['image'].'.jpg'">
<article><x-page-heading :eyebrow="$story['category']" :title="$story['title']" :intro="$story['excerpt']" :back-url="route('stories')" :back-label="$site->text('story.text_001')"/><figure class="wrap article-photo"><x-photo :name="$story['image']" :alt="$story['alt']" :eager="true" sizes="(min-width: 1280px) 1200px, 100vw"/><figcaption>{{ $story['alt'] }}</figcaption></figure><div class="article-body">@foreach($story['paragraphs'] as $paragraph)<p>{{ $paragraph }}</p>@endforeach<a class="button" href="{{ route('contact') }}">{{ $site->text('story.text_002') }}</a></div></article>
@if($slug === 'students-championing-peace')
    <section class="section wrap" aria-labelledby="peace-gallery-heading">
        <div class="section-heading"><div><p class="eyebrow">{{ $site->text('story.text_003') }}</p><h2 id="peace-gallery-heading">{{ $site->text('story.text_004') }}</h2></div><p>{{ $site->text('story.text_005') }}<br>{{ $site->text('story.text_006') }}</p></div>
        <div class="activity-gallery grid items-start gap-8 md:grid-cols-2">
            @foreach($site->gallery('peace') as $photo)
                <figure>
                    <x-photo :name="$photo['name']" :alt="$photo['alt']" sizes="(min-width: 1280px) 584px, (min-width: 768px) 50vw, 100vw"/>
                    <figcaption>{{ $photo['alt'] }}</figcaption>
                </figure>
            @endforeach
        </div>
    </section>
@endif
<x-partnership/>
</x-layout>
