@extends('layouts.admin')
@php($isEdit = $user->exists)
@section('title', $isEdit ? 'تعديل مستخدم' : 'مستخدم جديد')

@section('content')
<div class="page-head"><h1>{{ $isEdit ? 'تعديل المستخدم: ' . $user->name : 'إضافة مستخدم' }}</h1></div>
<form method="post" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" class="card" style="max-width:860px">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="card-b">
        <div class="row2">
            <x-a.input name="name" label="الاسم الكامل" :value="$user->name" required />
            <x-a.input name="email" label="البريد الإلكتروني" type="email" :value="$user->email" required dir="ltr" style="text-align:right" />
        </div>
        <div class="row2">
            <div class="field"><label>الدور الوظيفي (يحدد الصلاحيات) <span style="color:var(--red)">*</span></label>
                <select name="role_id" required><option value="">— اختر —</option>
                    @foreach($roles as $r)<option value="{{ $r->id }}" @selected(old('role_id', $user->role_id) == $r->id)>{{ $r->title }}</option>@endforeach
                </select>
                <div class="hint">تُعدَّل صلاحيات كل دور من «الأدوار والصلاحيات».</div>
            </div>
            <x-a.input name="job_title" label="المسمى الوظيفي المعروض" :value="$user->job_title" placeholder="مثال: محرر الشؤون الاقتصادية" />
        </div>
        <div class="row2">
            <x-a.input name="password" label="{{ $isEdit ? 'كلمة مرور جديدة (اختياري)' : 'كلمة المرور' }}" type="password" :required="! $isEdit" hint="8 أحرف على الأقل." autocomplete="new-password" />
            <x-a.input name="phone" label="الهاتف" :value="$user->phone" dir="ltr" style="text-align:right" />
        </div>
        <x-a.input name="twitter" label="حساب إكس (تويتر)" :value="$user->twitter" dir="ltr" style="text-align:right" />
        <x-a.textarea name="bio" label="نبذة تعريفية" :value="$user->bio" rows="3" maxlength="600" />
        @if($user->avatar)<img src="{{ $user->avatar_url }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin-bottom:8px" alt="">@endif
        <x-a.input name="avatar" label="الصورة الشخصية" type="file" accept="image/*" />
        <x-a.check name="is_active" label="الحساب فعّال" hint="تعطيل الحساب يمنع صاحبه من الدخول فوراً دون حذف مواده." :checked="(bool) $user->is_active" />
    </div>
    <div class="sticky-actions"><button class="btn btn-red">حفظ</button><a class="btn btn-ghost" href="{{ route('admin.users.index') }}">إلغاء</a></div>
</form>
@endsection
