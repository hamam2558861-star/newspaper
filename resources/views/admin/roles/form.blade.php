@extends('layouts.admin')
@php($isEdit = $role->exists)
@section('title', $isEdit ? 'تعديل دور' : 'دور جديد')

@section('content')
<div class="page-head"><h1>{{ $isEdit ? 'تعديل صلاحيات: ' . $role->title : 'إضافة دور وظيفي' }}</h1></div>
<form method="post" action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="card" style="max-width:900px">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="card-b">
        <div class="row2">
            <x-a.input name="title" label="اسم الدور / المسمى الوظيفي" :value="$role->title" required />
            <x-a.input name="description" label="وصف مختصر" :value="$role->description" />
        </div>
        <h3 style="margin:10px 0 14px">الصلاحيات</h3>
        @php($current = old('permissions', $role->permissions ?? []))
        @foreach(config('newsroom.permissions') as $group => $perms)
            <div class="perm-group">
                <h4>{{ $group }}</h4>
                <div class="perm-grid">
                    @foreach($perms as $key => $label)
                        <label class="check"><input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $current))><span>{{ $label }}<small dir="ltr" style="text-align:right">{{ $key }}</small></span></label>
                    @endforeach
                </div>
            </div>
        @endforeach
        <p class="hint" style="color:var(--muted)">ملاحظة: الدخول للوحة التحكم يتطلب صلاحية واحدة على الأقل. «اعتماد ونشر» تمنح صاحبها صلاحية المراجعة والنشر وتفعيل العاجل والمميز.</p>
    </div>
    <div class="sticky-actions"><button class="btn btn-red">حفظ</button><a class="btn btn-ghost" href="{{ route('admin.roles.index') }}">إلغاء</a></div>
</form>
@endsection
