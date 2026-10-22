{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ setting('site_name', config('app.name')) }}</title>
    <link>{{ route('home') }}</link>
    <description>{{ setting('site_description') }}</description>
    <language>ar</language>
    <atom:link href="{{ route('rss') }}" rel="self" type="application/rss+xml"/>
    @foreach($items as $a)
    <item>
        <title>{{ $a->title }}</title>
        <link>{{ $a->url() }}</link>
        <guid isPermaLink="true">{{ $a->url() }}</guid>
        <pubDate>{{ $a->published_at->toRfc2822String() }}</pubDate>
        <category>{{ $a->category?->name }}</category>
        <description><![CDATA[{{ $a->excerpt }}]]></description>
    </item>
    @endforeach
</channel>
</rss>
