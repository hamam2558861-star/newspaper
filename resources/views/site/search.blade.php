@extends('layouts.site')
@section('title', $term ? 'نتائج البحث عن: ' . $term : 'البحث')

@section('content')
<div class="container">
    <div class="page-title">
        <h1>البحث</h1>
        <form action="{{ route('search') }}" method="get" style="display:flex;gap:10px;margin-top:14px;flex-wrap:wrap">
            <input type="search" name="q" value="{{ $term }}" placeholder="اكتب كلمة البحث..." style="flex:1;min-width:220px;padding:11px 18px;border-radius:999px;border:0;font-family:inherit">
            <select name="category" style="padding:11px 16px;border-radius:999px;border:0;font-family:inherit">
                <option value="">كل الأقسام</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
            </select>
            <button class="btn" type="submit">بحث</button>
        </form>
    </div>
    <div class="layout">
        <section>
            @if($articles === null)
                <div class="empty">اكتب كلمتين على الأقل للبدء بالبحث.</div>
            @else
                <p style="margin-top:0;color:var(--muted)">{{ $articles->total() }} نتيجة للبحث عن «{{ $term }}»</p>
                @forelse($articles as $a)<x-card :a="$a" variant="list" />@empty
                    <div class="empty">لم نجد نتائج مطابقة. جرّب كلمات أخرى.</div>
                @endforelse
                {{ $articles->links() }}
            @endif
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
