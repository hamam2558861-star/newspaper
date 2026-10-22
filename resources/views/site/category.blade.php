@extends('layouts.site')
@section('title', $category->name)
@section('description', $category->description)

@section('content')
<div class="container">
    <div class="page-title" style="--c: {{ $category->color }}">
        @if($category->parent)<div class="kicker" style="color:#fff;opacity:.8"><a href="{{ $category->parent->url() }}">{{ $category->parent->name }}</a> ›</div>@endif
        <h1>{{ $category->name }}</h1>
        @if($category->description)<p>{{ $category->description }}</p>@endif
        @if($category->children->isNotEmpty())
            <div class="subcats">@foreach($category->children as $c)<a href="{{ $c->url() }}">{{ $c->name }}</a>@endforeach</div>
        @endif
    </div>
    <div class="layout">
        <section>
            @forelse($articles as $a)
                <x-card :a="$a" variant="list" />
            @empty
                <div class="empty">لا توجد مواد في هذا القسم حتى الآن.</div>
            @endforelse
            {{ $articles->links() }}
        </section>
        <aside class="sidebar">
            <x-ad position="sidebar_top" />
            <div class="most-read" style="--c: {{ $category->color }}">
                <div class="sec-head"><h2>الأكثر قراءة في {{ $category->name }}</h2></div>
                <ol>@foreach($mostRead as $a)<li><a href="{{ $a->url() }}">{{ $a->title }}</a></li>@endforeach</ol>
            </div>
            <x-ad position="sidebar_bottom" />
        </aside>
    </div>
</div>
@endsection
