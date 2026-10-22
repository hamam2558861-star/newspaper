@extends('layouts.admin')
@section('title', 'الأقسام')

@section('content')
<div class="page-head"><div><h1>الأقسام</h1><p>تنظيم أقسام الموقع وقائمة التصفح.</p></div><a class="btn btn-red" href="{{ route('admin.categories.create') }}">＋ قسم جديد</a></div>
<div class="card tbl-wrap">
    <table class="tbl">
        <thead><tr><th>الترتيب</th><th>القسم</th><th>الرابط</th><th>القسم الأب</th><th>المواد</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
        @foreach($categories as $c)
            <tr>
                <td>{{ $c->sort_order }}</td>
                <td><span class="dot" style="background:{{ $c->color }}"></span><strong>{{ $c->parent_id ? '— ' : '' }}{{ $c->name }}</strong></td>
                <td dir="ltr" style="text-align:right"><span class="sub">/section/{{ $c->slug }}</span></td>
                <td>{{ $c->parent?->name ?? '—' }}</td>
                <td>{{ $c->articles_count }}</td>
                <td>
                    <span class="badge {{ $c->is_active ? 'b-ok' : '' }}">{{ $c->is_active ? 'فعّال' : 'مخفي' }}</span>
                    @if($c->show_in_menu)<span class="badge b-info">في القائمة</span>@endif
                </td>
                <td><div class="actions">
                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.categories.edit', $c) }}">تعديل</a>
                    <form method="post" action="{{ route('admin.categories.destroy', $c) }}" data-confirm="حذف هذا القسم؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
                </div></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
