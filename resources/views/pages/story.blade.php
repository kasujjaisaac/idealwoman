<x-layout :title="$story['title']" :description="$story['excerpt']" :image="$story['image'].'.jpg'">
<article><x-page-heading :eyebrow="$story['category']" :title="$story['title']" :intro="$story['excerpt']" :back-url="route('stories')" :back-label="$site->text('story.text_001')"/><figure class="wrap article-photo"><x-photo :name="$story['image']" :alt="$story['alt']" :eager="true" sizes="(min-width: 1280px) 1200px, 100vw"/><figcaption>{{ $story['alt'] }}</figcaption></figure><div class="article-body">@foreach($story['paragraphs'] as $paragraph)<p>{{ $paragraph }}</p>@endforeach<a class="button" href="{{ route('contact') }}">{{ $site->text('story.text_002') }}</a></div></article>
@if($slug === 'students-championing-peace')
    <section class="section wrap" aria-labelledby="peace-gallery-heading">
        <div class="section-heading"><div><p class="eyebrow">{{ $site->text('story.text_003') }}</p><h2 id="peace-gallery-heading">{{ $site->text('story.text_004') }}</h2></div><p>{{ $site->text('story.text_005') }}<br>{{ $site->text('story.text_006') }}</p></div>
        <div class="activity-gallery grid items-start gap-8 md:grid-cols-2">
            @foreach([
                ['name' => 'peace-club-students', 'alt' => 'Students in orange shirts and adults gathered beside a Girl Child in Peace Building club sign.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-club-community', 'alt' => 'A group of students and adults posing together in a school garden beside the peace-building club sign.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-club-advocate', 'alt' => 'An adult standing behind a sign reading Our Peace, Our Responsibility and Girl Child in Peace Building.', 'width' => 1063, 'height' => 1600],
                ['name' => 'peace-club-mentorship', 'alt' => 'A student and an adult standing together behind the peace-building club sign.', 'width' => 1063, 'height' => 1600],
                ['name' => 'peace-club-friends', 'alt' => 'Students standing together beside the Girl Child in Peace Building club sign.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-club-youth', 'alt' => 'Young people and adults gathered around a Students Championing Peace club sign.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-youth-community', 'alt' => 'A larger group of young people and adults posing beside the peace-building club sign outdoors.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-school-club', 'alt' => 'Students and adults gathered on a school lawn beside a Students Championing Peace club sign.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-school-visitors', 'alt' => 'Adults standing together on a school lawn behind a peace-building club sign.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-school-team', 'alt' => 'Students and adults posing beside a peace-building club sign outside a school building.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-school-gathering', 'alt' => 'A large group of students and adults gathered in a school courtyard around a peace-building club sign.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-school-together', 'alt' => 'Students in orange shirts and school uniforms standing with adults in a school courtyard.', 'width' => 1600, 'height' => 1063],
                ['name' => 'peace-school-connection', 'alt' => 'A student and an adult standing together on the grass outside a school building.', 'width' => 1063, 'height' => 1600],
                ['name' => 'peace-student-voice', 'alt' => 'A student in an orange shirt standing behind the Girl Child in Peace Building club sign.', 'width' => 1063, 'height' => 1600],
            ] as $photo)
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
