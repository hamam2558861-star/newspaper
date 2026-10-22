@extends('layouts.admin')
@section('title', 'الأدوار والصلاحيات')

@section('content')
<div class="page-head"><div><h1>الأدوار والصلاحيات</h1><p>حدد ما يستطيع كل مسمى وظيفي فعله داخل لوحة التحكم.</p></div><a class="btn btn-red" href="{{ route('admin.roles.create') }}">＋ دور جديد</a></div>
@php($labels = collect(config('newsroom.permissions'))->flatMap(fn ($g) => $g))
<div class="card tbl-wrap"><table class="tbl">
    <thead><tr><th>الدور</th><th>الصلاحيات</th><th>المستخدمون</th><th></th></tr></thead>
    <tbody>
    @foreach($roles as $r)
        <tr>
            <td style="min-width:200px"><strong>{{ $r->title }}</strong>@if($r->is_system) <span class="badge">نظامي</span>@endif<div class="sub">{{ $r->description }}</div></td>
            <td style="max-width:520px">
                @if(in_array('*', $r->permissions ?? []))<span class="badge b-ok">كل الصلاحيات</span>
                @else<div style="display:flex;gap:4px;flex-wrap:wrap">@forelse($r->permissions ?? [] as $p)<span class="badge b-info">{{ $labels[$p] ?? $p }}</span>@empty<span class="sub">لا شيء</span>@endforelse</div>@endif
            </td>
            <td>{{ $r->users_count }}</td>
            <td><div class="actions">
                @if($r->name !== 'super_admin')<a class="btn btn-ghost btn-sm" href="{{ route('admin.roles.edit', $r) }}">تعديل</a>@endif
                @if(! $r->is_system && ! $r->users_count)
                <form method="post" action="{{ route('admin.roles.destroy', $r) }}" data-confirm="حذف الدور؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
                @endif
            </div></td>
        </tr>
    @endforeach
    </tbody>
</table></div>
@endsection
