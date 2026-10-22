@extends('layouts.admin')
@section('title', 'ملفي الشخصي')

@section('content')
<div class="page-head"><h1>ملفي الشخصي</h1></div>
<form method="post" enctype="multipart/form-data" action="{{ route('admin.profile.update') }}" class="card" style="max-width:820px">
    @csrf @method('PUT')
    <div class="card-b">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px">
            <img src="{{ $user->avatar_url }}" style="width:72px;height:72px;border-radius:50%;object-fit:cover" alt="">
            <div><strong style="color:var(--ink);font-size:18px">{{ $user->name }}</strong><br><span class="badge b-info">{{ $user->role?->title }}</span></div>
        </div>
        <div class="row2">
            <x-a.input name="name" label="الاسم" :value="$user->name" required />
            <x-a.input name="email" label="البريد الإلكتروني" type="email" :value="$user->email" required dir="ltr" style="text-align:right" />
        </div>
        <div class="row2">
            <x-a.input name="job_title" label="المسمى الوظيفي" :value="$user->job_title" />
            <x-a.input name="phone" label="الهاتف" :value="$user->phone" dir="ltr" style="text-align:right" />
        </div>
        <x-a.input name="twitter" label="حساب إكس" :value="$user->twitter" dir="ltr" style="text-align:right" />
        <x-a.textarea name="bio" label="نبذة تعريفية" :value="$user->bio" rows="3" />
        <x-a.input name="avatar" label="تغيير الصورة" type="file" accept="image/*" />
        <h3 style="margin:18px 0 12px">تغيير كلمة المرور</h3>
        <div class="row3">
            <x-a.input name="current_password" label="الحالية" type="password" autocomplete="current-password" />
            <x-a.input name="password" label="الجديدة" type="password" autocomplete="new-password" />
            <x-a.input name="password_confirmation" label="تأكيد الجديدة" type="password" autocomplete="new-password" />
        </div>
    </div>
    <div class="sticky-actions"><button class="btn btn-red">حفظ التغييرات</button></div>
</form>
@endsection
