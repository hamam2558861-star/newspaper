@extends('layouts.admin')
@php($isEdit = $ad->exists)
@section('title', $isEdit ? 'تعديل إعلان' : 'إعلان جديد')

@section('content')
<div class="page-head"><h1>{{ $isEdit ? 'تعديل الإعلان' : 'إضافة إعلان' }}</h1></div>
<form method="post" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.ads.update', $ad) : route('admin.ads.store') }}" class="card" style="max-width:820px">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="card-b">
        <div class="row2">
            <x-a.input name="title" label="عنوان الإعلان (داخلي)" :value="$ad->title" required />
            <div class="field"><label>موضع الظهور</label>
                <select name="position" required>@foreach(config('newsroom.ad_positions') as $k => $v)<option value="{{ $k }}" @selected(old('position', $ad->position) === $k)>{{ $v }}</option>@endforeach</select>
            </div>
        </div>
        @if($ad->image)<img src="{{ upload_url($ad->image) }}" style="max-height:120px;border-radius:8px;margin-bottom:10px" alt="">@endif
        <x-a.input name="image" label="صورة الإعلان" type="file" accept="image/*" />
        <x-a.input name="url" label="رابط الوجهة عند النقر" type="url" :value="$ad->url" dir="ltr" />
        <x-a.textarea name="code" label="أو كود إعلاني (HTML / AdSense)" :value="$ad->code" rows="4" dir="ltr" hint="يُستخدم إن لم تُرفع صورة. ينفَّذ كما هو، فلا تلصق إلا أكواداً موثوقة." />
        <div class="row2">
            <x-a.input name="starts_at" label="بداية الحملة" type="datetime-local" :value="$ad->starts_at?->format('Y-m-d\TH:i')" />
            <x-a.input name="ends_at" label="نهاية الحملة" type="datetime-local" :value="$ad->ends_at?->format('Y-m-d\TH:i')" />
        </div>
        <x-a.check name="is_active" label="الإعلان فعّال" :checked="(bool) $ad->is_active" />
    </div>
    <div class="sticky-actions"><button class="btn btn-red">حفظ</button><a class="btn btn-ghost" href="{{ route('admin.ads.index') }}">إلغاء</a></div>
</form>
@endsection
