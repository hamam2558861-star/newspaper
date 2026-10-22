@extends('layouts.admin')
@section('title', 'سجل النشاط')

@section('content')
<div class="page-head"><div><h1>سجل النشاط</h1><p>تتبّع من فعل ماذا ومتى داخل لوحة التحكم.</p></div></div>
<div class="card">
    <form class="filters" method="get">
        <select name="action"><option value="">كل الإجراءات</option>@foreach($actions as $a)<option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>@endforeach</select>
        <button class="btn btn-sm">تصفية</button>
    </form>
    <div class="tbl-wrap"><table class="tbl">
        <thead><tr><th>الوقت</th><th>المستخدم</th><th>الإجراء</th><th>التفاصيل</th><th>IP</th></tr></thead>
        <tbody>
        @forelse($logs as $l)
            <tr>
                <td><span class="sub">{{ $l->created_at->format('Y/m/d H:i') }}</span></td>
                <td>{{ $l->user?->name ?? 'النظام' }}</td>
                <td><span class="badge b-info">{{ $l->action }}</span></td>
                <td>{{ $l->description }}</td>
                <td dir="ltr" style="text-align:right"><span class="sub">{{ $l->ip }}</span></td>
            </tr>
        @empty<tr><td colspan="5"><div class="empty">لا توجد سجلات.</div></td></tr>@endforelse
        </tbody>
    </table></div>
    {{ $logs->links() }}
</div>
@endsection
