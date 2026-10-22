@extends('layouts.admin')
@section('title', 'التعليقات')

@section('content')
<div class="page-head"><div><h1>إدارة التعليقات</h1><p>راجع تعليقات القرّاء قبل ظهورها على الموقع.</p></div></div>
<div class="tabs">
    @foreach(['pending' => 'بانتظار الموافقة', 'approved' => 'معتمدة', 'spam' => 'مزعجة', 'all' => 'الكل'] as $k => $v)
        <a href="{{ route('admin.comments.index', ['status' => $k]) }}" class="{{ $status === $k ? 'on' : '' }}">{{ $v }}@if($k !== 'all') ({{ $counts[$k] ?? 0 }})@endif</a>
    @endforeach
</div>
<div class="card">
    @forelse($comments as $c)
        <div class="list-row" style="align-items:flex-start;padding:16px 20px">
            <div style="flex:1">
                <div><strong style="color:var(--ink)">{{ $c->name }}</strong> <span class="sub" style="color:var(--muted)">{{ $c->email }} · {{ $c->created_at->diffForHumans() }}</span>
                    <span class="badge {{ ['approved' => 'b-ok', 'pending' => 'b-warn', 'spam' => 'b-err'][$c->status] ?? '' }}">{{ ['approved' => 'معتمد', 'pending' => 'بانتظار الموافقة', 'spam' => 'مزعج'][$c->status] ?? $c->status }}</span></div>
                <p style="margin:6px 0">{{ $c->body }}</p>
                <a class="sub" style="color:var(--info);font-size:13px" href="{{ $c->article->url() }}#comments" target="_blank">على: {{ \Illuminate\Support\Str::limit($c->article->title, 70) }}</a>
            </div>
            <div class="actions">
                @if($c->status !== 'approved')<form method="post" action="{{ route('admin.comments.status', [$c, 'approved']) }}">@csrf<button class="btn btn-green btn-sm">اعتماد</button></form>@endif
                @if($c->status !== 'spam')<form method="post" action="{{ route('admin.comments.status', [$c, 'spam']) }}">@csrf<button class="btn btn-ghost btn-sm">مزعج</button></form>@endif
                <form method="post" action="{{ route('admin.comments.destroy', $c) }}" data-confirm="حذف التعليق نهائياً؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
            </div>
        </div>
    @empty<div class="empty">لا توجد تعليقات في هذه الفئة.</div>@endforelse
    {{ $comments->links() }}
</div>
@endsection
