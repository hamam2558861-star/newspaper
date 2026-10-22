@extends('layouts.admin')
@section('title', 'المشتركون')

@section('content')
<div class="page-head"><div><h1>المشتركون في النشرة</h1></div><a class="btn" href="{{ route('admin.subscribers.export') }}">⬇ تصدير CSV</a></div>
<div class="card">
    <form class="filters" method="get"><input type="search" name="q" value="{{ request('q') }}" placeholder="بحث بالبريد..."><button class="btn btn-sm">بحث</button></form>
    <div class="tbl-wrap"><table class="tbl">
        <thead><tr><th>البريد الإلكتروني</th><th>تاريخ الاشتراك</th><th></th></tr></thead>
        <tbody>
        @forelse($subs as $s)
            <tr><td dir="ltr" style="text-align:right">{{ $s->email }}</td><td>{{ $s->created_at->format('Y/m/d') }}</td>
                <td><form method="post" action="{{ route('admin.subscribers.destroy', $s) }}" data-confirm="حذف المشترك؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form></td></tr>
        @empty<tr><td colspan="3"><div class="empty">لا يوجد مشتركون.</div></td></tr>@endforelse
        </tbody>
    </table></div>
    {{ $subs->links() }}
</div>
@endsection
