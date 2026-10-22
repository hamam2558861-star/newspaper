@extends('layouts.site')

@section('content')
<div class="container">

    {{-- القسم البطولي --}}
    @if($hero->isNotEmpty())
    @php($main = $hero->first())
    <section class="hero">
        <a href="{{ $main->url() }}" class="hero-main" style="--c: {{ $main->category->color ?? '#c8102e' }}">
            <x-thumb :a="$main" />
            <div class="hero-body">
                @if($main->category)<span class="badge">{{ $main->category->name }}</span>@endif
                <h2>{{ $main->title }}</h2>
                <p>{{ \Illuminate\Support\Str::limit($main->excerpt, 180) }}</p>
                <div class="meta"><span>{{ $main->published_at->diffForHumans() }}</span><span>{{ number_format($main->views) }} مشاهدة</span></div>
            </div>
        </a>
        <div class="hero-side">
            @foreach($hero->slice(1, 2) as $a)<x-card :a="$a" />@endforeach
        </div>
    </section>
    @if($hero->count() > 3)
    <div class="hero-strip">
        @foreach($hero->slice(3) as $a)<x-card :a="$a" variant="row" />@endforeach
    </div>
    @endif
    @endif

    {{-- آخر الأخبار + الشريط الجانبي --}}
    <div class="layout section">
        <section>
            <div class="sec-head"><h2>آخر الأخبار</h2><a class="more" href="{{ route('latest') }}">المزيد ←</a></div>
            @foreach($latest as $a)<x-card :a="$a" variant="list" />@endforeach
        </section>
        <aside class="sidebar">
            <x-ad position="sidebar_top" />
            <div class="most-read">
                <div class="sec-head"><h2>الأكثر قراءة</h2></div>
                <ol>@foreach($mostRead as $a)<li><a href="{{ $a->url() }}">{{ $a->title }}</a></li>@endforeach</ol>
            </div>
            @if($trendingTags->isNotEmpty())
            <div class="panel">
                <h3 style="margin-bottom:12px;font-size:18px">المواضيع الرائجة</h3>
                <div class="tags">@foreach($trendingTags as $t)<a href="{{ route('tag.show', $t->slug) }}">{{ $t->name }}</a>@endforeach</div>
            </div>
            @endif
            <x-ad position="sidebar_bottom" />
        </aside>
    </div>

    {{-- أقسام الموقع --}}
    @foreach($sections as $cat)
    @php($first = $cat->items->first())
    <section class="section" style="--c: {{ $cat->color }}">
        <div class="sec-head"><h2>{{ $cat->name }}</h2><a class="more" href="{{ $cat->url() }}">كل أخبار {{ $cat->name }} ←</a></div>
        <div class="cat-block">
            <x-card :a="$first" :excerpt="true" :show-cat="false" />
            <div class="stack">
                @foreach($cat->items->slice(1) as $a)<x-card :a="$a" variant="row" :show-cat="false" />@endforeach
            </div>
        </div>
    </section>
    @endforeach

    {{-- الرأي --}}
    @if($opinions->isNotEmpty())
    <section class="section opinions">
        <div class="sec-head"><h2>رأي وكتّاب</h2><a class="more" href="{{ route('opinions') }}" style="color:#fda4af">كل المقالات ←</a></div>
        <div class="grid g4">
            @foreach($opinions as $a)
            <a href="{{ $a->url() }}" class="op-card">
                <img src="{{ $a->author->avatar_url }}" alt="{{ $a->author->name }}">
                <div class="who">{{ $a->author->name }}</div>
                <div class="job">{{ $a->author->job_title }}</div>
                <h3>{{ $a->title }}</h3>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- الفيديو --}}
    @if($videos->isNotEmpty())
    <section class="section videos">
        <div class="sec-head"><h2>فيديو</h2><a class="more" href="{{ route('videos') }}" style="color:#fda4af">كل الفيديوهات ←</a></div>
        <div class="grid g4">
            @foreach($videos as $a)<x-card :a="$a" />@endforeach
        </div>
    </section>
    @endif
</div>
@endsection
