@extends('layouts.admin')
@php($isEdit = $page->exists)
@section('title', $isEdit ? 'تعديل صفحة' : 'صفحة جديدة')

@push('head')<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">@endpush
@push('scripts')<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>@endpush

@section('content')
<div class="page-head"><h1>{{ $isEdit ? 'تعديل الصفحة' : 'صفحة جديدة' }}</h1></div>
<form method="post" action="{{ $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="card" style="max-width:980px">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="card-b">
        <div class="row2">
            <x-a.input name="title" label="العنوان" :value="$page->title" required />
            <x-a.input name="slug" label="الرابط (إنجليزي)" :value="$page->slug" dir="ltr" hint="اتركه فارغاً للتوليد التلقائي." />
        </div>
        <div class="field">
            <label>المحتوى</label>
            <div id="editor" data-upload="{{ route('admin.upload.image') }}"></div>
            <input type="hidden" name="body" id="body-input" value="{{ old('body', $page->body) }}">
            @error('body')<div class="err">{{ $message }}</div>@enderror
        </div>
        <x-a.check name="show_in_footer" label="إظهار في تذييل الموقع" :checked="(bool) $page->show_in_footer" />
        <x-a.check name="is_active" label="الصفحة منشورة" :checked="(bool) $page->is_active" />
    </div>
    <div class="sticky-actions"><button class="btn btn-red">حفظ</button><a class="btn btn-ghost" href="{{ route('admin.pages.index') }}">إلغاء</a></div>
</form>
@endsection
