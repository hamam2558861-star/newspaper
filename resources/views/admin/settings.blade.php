@extends('layouts.admin')
@section('title', 'إعدادات الموقع')

@section('content')
<div class="page-head"><h1>إعدادات الموقع</h1></div>
<form method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf @method('PUT')
    <div class="card"><div class="card-h"><h3>الهوية</h3></div><div class="card-b">
        <div class="row2">
            <x-a.input name="site_name" label="اسم الموقع / الصحيفة" :value="setting('site_name')" required />
            <x-a.input name="site_tagline" label="الشعار النصي" :value="setting('site_tagline')" />
        </div>
        <x-a.textarea name="site_description" label="وصف الموقع (SEO)" :value="setting('site_description')" rows="2" />
        <div class="row2">
            <div>@if(setting('logo'))<img src="{{ upload_url(setting('logo')) }}" style="max-height:50px;margin-bottom:8px" alt="">@endif<x-a.input name="logo" label="الشعار (Logo)" type="file" accept="image/*" /></div>
            <div>@if(setting('favicon'))<img src="{{ upload_url(setting('favicon')) }}" style="max-height:32px;margin-bottom:8px" alt="">@endif<x-a.input name="favicon" label="أيقونة الموقع (Favicon)" type="file" accept="image/*" /></div>
        </div>
    </div></div>

    <div class="card" style="margin-top:20px"><div class="card-h"><h3>بيانات التواصل والتذييل</h3></div><div class="card-b">
        <div class="row3">
            <x-a.input name="contact_email" label="البريد" type="email" :value="setting('contact_email')" dir="ltr" style="text-align:right" />
            <x-a.input name="contact_phone" label="الهاتف" :value="setting('contact_phone')" dir="ltr" style="text-align:right" />
            <x-a.input name="contact_address" label="العنوان" :value="setting('contact_address')" />
        </div>
        <x-a.textarea name="footer_about" label="نبذة التذييل" :value="setting('footer_about')" rows="2" />
        <x-a.input name="copyright" label="نص حقوق النشر" :value="setting('copyright')" />
    </div></div>

    <div class="card" style="margin-top:20px"><div class="card-h"><h3>التواصل الاجتماعي</h3></div><div class="card-b">
        <div class="row3">
            <x-a.input name="facebook" label="فيسبوك" type="url" :value="setting('facebook')" dir="ltr" />
            <x-a.input name="x_twitter" label="إكس" type="url" :value="setting('x_twitter')" dir="ltr" />
            <x-a.input name="instagram" label="إنستغرام" type="url" :value="setting('instagram')" dir="ltr" />
            <x-a.input name="youtube" label="يوتيوب" type="url" :value="setting('youtube')" dir="ltr" />
            <x-a.input name="telegram" label="تيليغرام" type="url" :value="setting('telegram')" dir="ltr" />
        </div>
    </div></div>

    <div class="card" style="margin-top:20px"><div class="card-h"><h3>المحتوى والتعليقات</h3></div><div class="card-b">
        <x-a.input name="articles_per_page" label="عدد المواد في الصفحة" type="number" :value="setting('articles_per_page', 12)" />
        <x-a.check name="comments_enabled" label="تفعيل التعليقات على الموقع" :checked="setting('comments_enabled', '1') === '1'" />
        <x-a.check name="comments_auto_approve" label="نشر التعليقات تلقائياً دون مراجعة" hint="غير مُفضّل؛ الأفضل أن يراجعها مشرف التفاعل." :checked="setting('comments_auto_approve', '0') === '1'" />
    </div></div>

    <div class="card" style="margin-top:20px"><div class="card-h"><h3>متقدّم</h3></div><div class="card-b">
        <x-a.textarea name="head_code" label="كود مخصص داخل &lt;head&gt; (Google Analytics وغيره)" :value="setting('head_code')" rows="4" dir="ltr" />
    </div></div>

    <div class="card" style="margin-top:20px"><div class="sticky-actions"><button class="btn btn-red">حفظ الإعدادات</button></div></div>
</form>
@endsection
