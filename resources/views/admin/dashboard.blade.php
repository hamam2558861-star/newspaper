@extends('layouts.admin')
@section('title', 'لوحة المعلومات')

@section('content')
@php($max = max(1, $chart->max('value')))
<div class="page-head">
    <div>
        <h1>مرحباً، {{ auth()->user()->name }}</h1>
        <p>{{ auth()->user()->role?->title }} — {{ ar_date(now(), 'l، j F Y') }}</p>
    </div>
    @can('articles.create')<a class="btn btn-red" href="{{ route('admin.articles.create') }}">＋ كتابة مادة جديدة</a>@endcan
</div>

<div class="stats">
    <div class="card stat"><div class="ic" style="background:var(--ok-bg)">✔</div><div><div class="v">{{ number_format($stats['published']) }}</div><div class="l">مواد منشورة</div></div></div>
    <div class="card stat"><div class="ic" style="background:var(--warn-bg)">⏳</div><div><div class="v">{{ number_format($stats['pending']) }}</div><div class="l">بانتظار المراجعة</div></div></div>
    <div class="card stat"><div class="ic" style="background:#f2f4f7">✎</div><div><div class="v">{{ number_format($stats['drafts']) }}</div><div class="l">مسودات</div></div></div>
    <div class="card stat"><div class="ic">👁</div><div><div class="v">{{ number_format($stats['views']) }}</div><div class="l">إجمالي المشاهدات</div></div></div>
</div>

@if(count($extra))
<div class="stats">
    @isset($extra['pending_comments'])<a href="{{ route('admin.comments.index') }}" class="card stat"><div class="ic" style="background:var(--warn-bg)">☁</div><div><div class="v">{{ $extra['pending_comments'] }}</div><div class="l">تعليقات بانتظار الموافقة</div></div></a>@endisset
    @isset($extra['unread_messages'])<a href="{{ route('admin.messages.index') }}" class="card stat"><div class="ic">✉</div><div><div class="v">{{ $extra['unread_messages'] }}</div><div class="l">رسائل غير مقروءة</div></div></a>@endisset
    @isset($extra['subscribers'])<a href="{{ route('admin.subscribers.index') }}" class="card stat"><div class="ic" style="background:var(--ok-bg)">☺</div><div><div class="v">{{ $extra['subscribers'] }}</div><div class="l">مشترك في النشرة</div></div></a>@endisset
    @isset($extra['users'])<a href="{{ route('admin.users.index') }}" class="card stat"><div class="ic">☻</div><div><div class="v">{{ $extra['users'] }}</div><div class="l">مستخدمو النظام</div></div></a>@endisset
</div>
@endif

<div class="grid2">
    <div class="card">
        <div class="card-h"><h3>المواد المنشورة — آخر 14 يوماً</h3></div>
        <div class="card-b">
            <div class="bars">
                @foreach($chart as $p)
                    <div class="b"><em>{{ $p['value'] ?: '' }}</em><i style="height: {{ max(2, $p['value'] / $max * 100) }}%"></i><span>{{ $p['label'] }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-h"><h3>الأكثر قراءة (30 يوماً)</h3></div>
        @forelse($top as $a)
            <div class="list-row"><a class="t" href="{{ $a->url() }}" target="_blank">{{ \Illuminate\Support\Str::limit($a->title, 60) }}</a><span class="badge b-info">{{ number_format($a->views) }}</span></div>
        @empty<div class="empty">لا توجد بيانات بعد.</div>@endforelse
    </div>
</div>

<div class="grid2">
    @can('articles.publish')
    <div class="card">
        <div class="card-h"><h3>بانتظار المراجعة والاعتماد</h3><a class="btn btn-ghost btn-sm" href="{{ route('admin.articles.index', ['status' => 'pending']) }}">عرض الكل</a></div>
        @forelse($queue as $a)
            <div class="list-row">
                <div><a class="t" href="{{ route('admin.articles.edit', $a) }}">{{ \Illuminate\Support\Str::limit($a->title, 70) }}</a><div class="sub" style="font-size:12.5px;color:var(--muted)">{{ $a->author->name }} · {{ $a->category->name }} · {{ $a->updated_at->diffForHumans() }}</div></div>
                <a class="btn btn-sm" href="{{ route('admin.articles.edit', $a) }}">مراجعة</a>
            </div>
        @empty<div class="empty">لا توجد مواد بانتظار المراجعة 🎉</div>@endforelse
    </div>
    @endcan
    @can('articles.create')
    <div class="card">
        <div class="card-h"><h3>موادي الأخيرة</h3></div>
        @forelse($mine as $a)
            <div class="list-row">
                <a class="t" href="{{ route('admin.articles.edit', $a) }}">{{ \Illuminate\Support\Str::limit($a->title, 50) }}</a>
                @include('admin.articles._status', ['a' => $a])
            </div>
        @empty<div class="empty">لم تكتب أي مادة بعد.</div>@endforelse
    </div>
    @endcan
    @can('logs.view')
    <div class="card">
        <div class="card-h"><h3>آخر النشاطات</h3><a class="btn btn-ghost btn-sm" href="{{ route('admin.logs') }}">السجل الكامل</a></div>
        @foreach($logs as $l)
            <div class="list-row"><div><div class="t" style="color:var(--ink)">{{ $l->description }}</div><div style="font-size:12.5px;color:var(--muted)">{{ $l->user?->name ?? 'النظام' }}</div></div><span style="font-size:12px;color:var(--muted)">{{ $l->created_at->diffForHumans() }}</span></div>
        @endforeach
    </div>
    @endcan
</div>
@endsection
