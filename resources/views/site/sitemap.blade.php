{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc><changefreq>hourly</changefreq><priority>1.0</priority></url>
    @foreach($categories as $c)
    <url><loc>{{ route('category.show', $c->slug) }}</loc><lastmod>{{ $c->updated_at->toIso8601String() }}</lastmod><changefreq>hourly</changefreq><priority>0.8</priority></url>
    @endforeach
    @foreach($pages as $p)
    <url><loc>{{ route('page.show', $p->slug) }}</loc><lastmod>{{ $p->updated_at->toIso8601String() }}</lastmod><priority>0.3</priority></url>
    @endforeach
    @foreach($articles as $a)
    <url><loc>{{ $a->url() }}</loc><lastmod>{{ $a->updated_at->toIso8601String() }}</lastmod><priority>0.6</priority></url>
    @endforeach
</urlset>
