@extends('layouts.admin')
@section('title', 'رسائل التواصل')

@section('content')
<div class="page-head"><div><h1>رسائل التواصل</h1></div></div>
<div class="card tbl-wrap"><table class="tbl">
    <thead><tr><th>المرسل</th><th>الموضوع</th><th>التاريخ</th><th></th></tr></thead>
    <tbody>
    @forelse($messages as $m)
        <tr>
            <td><strong>{{ $m->name }}</strong>@unless($m->is_read) <span class="badge b-warn">جديدة</span>@endunless<div class="sub">{{ $m->email }}</div></td>
            <td><a class="title" href="{{ route('admin.messages.show', $m) }}" style="{{ $m->is_read ? 'font-weight:500' : '' }}">{{ $m->subject }}</a></td>
            <td><span class="sub">{{ $m->created_at->diffForHumans() }}</span></td>
            <td><div class="actions">
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.messages.show', $m) }}">قراءة</a>
                <form method="post" action="{{ route('admin.messages.destroy', $m) }}" data-confirm="حذف الرسالة؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
            </div></td>
        </tr>
    @empty<tr><td colspan="4"><div class="empty">لا توجد رسائل.</div></td></tr>@endforelse
    </tbody>
</table>{{ $messages->links() }}</div>
@endsection
