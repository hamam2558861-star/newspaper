@extends('layouts.admin')
@php($isEdit = $category->exists)
@section('title', $isEdit ? 'تعديل قسم' : 'قسم جديد')

@section('content')
<div class="page-head"><h1>{{ $isEdit ? 'تعديل القسم: ' . $category->name : 'إضافة قسم جديد' }}</h1></div>
<form method="post" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="card" style="max-width:760px">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="card-b">
        <div class="row2">
            <x-a.input name="name" label="اسم القسم" :value="$category->name" required />
            <x-a.input name="slug" label="الرابط (إنجليزي)" :value="$category->slug" dir="ltr" hint="اتركه فارغاً للتوليد التلقائي." />
        </div>
        <div class="row3">
            <div class="field"><label>القسم الأب</label>
                <select name="parent_id"><option value="">— قسم رئيسي —</option>@foreach($parents as $p)<option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id) == $p->id)>{{ $p->name }}</option>@endforeach</select>
            </div>
            <x-a.input name="color" label="لون القسم" type="color" :value="$category->color" />
            <x-a.input name="sort_order" label="الترتيب" type="number" :value="$category->sort_order ?? 0" />
        </div>
        <x-a.textarea name="description" label="وصف القسم" :value="$category->description" rows="3" />
        <x-a.check name="show_in_menu" label="إظهار في القائمة الرئيسية" :checked="(bool) $category->show_in_menu" />
        <x-a.check name="is_active" label="القسم فعّال" :checked="(bool) $category->is_active" />
    </div>
    <div class="sticky-actions"><button class="btn btn-red">حفظ</button><a class="btn btn-ghost" href="{{ route('admin.categories.index') }}">إلغاء</a></div>
</form>
@endsection
