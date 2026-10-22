@extends('layouts.admin')
@php($isEdit = $tag->exists)
@section('title', $isEdit ? 'تعديل وسم' : 'وسم جديد')

@section('content')
<div class="page-head"><h1>{{ $isEdit ? 'تعديل الوسم' : 'إضافة وسم' }}</h1></div>
<form method="post" action="{{ $isEdit ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" class="card" style="max-width:520px">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="card-b"><x-a.input name="name" label="اسم الوسم" :value="$tag->name" required /></div>
    <div class="sticky-actions"><button class="btn btn-red">حفظ</button><a class="btn btn-ghost" href="{{ route('admin.tags.index') }}">إلغاء</a></div>
</form>
@endsection
