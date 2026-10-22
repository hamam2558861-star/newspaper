@extends('layouts.site')
@section('title', $title)

@section('content')
<div class="container">
    <div class="page-title"><h1>{{ $title }}</h1></div>
    <div class="layout">
        <section>
            @forelse($articles as $a)
                <x-card :a="$a" variant="list" />
            @empty
                <div class="empty">لا توجد مواد منشورة بعد.</div>
            @endforelse
            {{ $articles->links() }}
        </section>
        <aside class="sidebar">
            <x-ad position="sidebar_top" />
            <div class="most-read">
                <div class="sec-head"><h2>الأكثر قراءة</h2></div>
                <ol>@foreach($mostRead as $a)<li><a href="{{ $a->url() }}">{{ $a->title }}</a></li>@endforeach</ol>
            </div>
            <x-ad position="sidebar_bottom" />
        </aside>
    </div>
</div>
@endsection
