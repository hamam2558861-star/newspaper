@extends('layouts.admin')
@section('title', 'الوسوم')

@section('content')
<div class="page-head"><div><h1>الوسوم</h1></div><a class="btn btn-red" href="{{ route('admin.tags.create') }}">＋ وسم جديد</a></div>
<div class="card">
    <form class="filters" method="get"><input type="search" name="q" value="{{ request('q') }}" placeholder="بحث..."><button class="btn btn-sm">بحث</button></form>
    <div class="tbl-wrap"><table class="tbl">
        <thead><tr><th>الوسم</th><th>عدد المواد</th><th></th></tr></thead>
        <tbody>
        @forelse($tags as $t)
            <tr><td><strong>#{{ $t->name }}</strong></td><td>{{ $t->articles_count }}</td>
                <td><div class="actions">
                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.tags.edit', $t) }}">تعديل</a>
                    <form method="post" action="{{ route('admin.tags.destroy', $t) }}" data-confirm="حذف الوسم؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
                </div></td></tr>
        @empty<tr><td colspan="3"><div class="empty">لا توجد وسوم.</div></td></tr>@endforelse
        </tbody>
    </table></div>
    {{ $tags->links() }}
</div>
@endsection
