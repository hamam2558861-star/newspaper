@extends('layouts.admin')
@section('title', 'الصفحات الثابتة')

@section('content')
<div class="page-head"><div><h1>الصفحات الثابتة</h1><p>من نحن، الخصوصية، الشروط وغيرها.</p></div><a class="btn btn-red" href="{{ route('admin.pages.create') }}">＋ صفحة جديدة</a></div>
<div class="card tbl-wrap"><table class="tbl">
    <thead><tr><th>العنوان</th><th>الرابط</th><th>التذييل</th><th>الحالة</th><th></th></tr></thead>
    <tbody>
    @foreach($pages as $p)
        <tr>
            <td><strong>{{ $p->title }}</strong></td>
            <td dir="ltr" style="text-align:right"><span class="sub">/page/{{ $p->slug }}</span></td>
            <td>{{ $p->show_in_footer ? 'نعم' : 'لا' }}</td>
            <td><span class="badge {{ $p->is_active ? 'b-ok' : '' }}">{{ $p->is_active ? 'منشورة' : 'مخفية' }}</span></td>
            <td><div class="actions">
                <a class="btn btn-ghost btn-sm" href="{{ route('page.show', $p->slug) }}" target="_blank">عرض</a>
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.pages.edit', $p) }}">تعديل</a>
                <form method="post" action="{{ route('admin.pages.destroy', $p) }}" data-confirm="حذف الصفحة؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
            </div></td>
        </tr>
    @endforeach
    </tbody>
</table></div>
@endsection
