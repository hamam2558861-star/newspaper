@extends('layouts.site')
@section('title', $author->name)

@section('content')
<div class="container">
    <div class="page-title">
        <div class="author-hero">
            <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}">
            <div>
                <h1>{{ $author->name }}</h1>
                <div style="opacity:.85">{{ $author->job_title ?: $author->role?->title }}</div>
                @if($author->bio)<p>{{ $author->bio }}</p>@endif
                @if($author->twitter)<p><a href="https://x.com/{{ ltrim($author->twitter, '@') }}" target="_blank" rel="noopener">&#64;{{ ltrim($author->twitter, '@') }}</a></p>@endif
            </div>
        </div>
    </div>
    <div class="layout">
        <section>
            @foreach($articles as $a)<x-card :a="$a" variant="list" />@endforeach
            {{ $articles->links() }}
        </section>
        <aside class="sidebar">
            <div class="most-read">
                <div class="sec-head"><h2>الأكثر قراءة</h2></div>
                <ol>@foreach($mostRead as $a)<li><a href="{{ $a->url() }}">{{ $a->title }}</a></li>@endforeach</ol>
            </div>
        </aside>
    </div>
</div>
@endsection
