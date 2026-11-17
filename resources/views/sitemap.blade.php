{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach(['home', 'about', 'work', 'stories', 'involved', 'contact'] as $page)
<url><loc>{{ route($page) }}</loc></url>
@endforeach
@foreach(config('idealwoman.stories') as $slug => $story)
<url><loc>{{ route('stories.show', $slug) }}</loc><image:image><image:loc>{{ $site->media($story['image'], asset('images/'.$story['image'].'.jpg')) }}</image:loc></image:image></url>
@endforeach
</urlset>
