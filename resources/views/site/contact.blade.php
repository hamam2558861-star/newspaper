@extends('layouts.site')
@section('title', 'اتصل بنا')

@section('content')
<div class="container">
    <div class="page-title"><h1>اتصل بنا</h1><p>يسعدنا تواصلكم معنا: ملاحظات، اقتراحات، أو أخبار تودّون إيصالها إلى هيئة التحرير.</p></div>
    <div class="contact-grid">
        <form action="{{ route('contact.store') }}" method="post">
            @csrf
            @if(session('success'))<div class="flash ok">{{ session('success') }}</div>@endif
            <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="form-row">
                <div class="field"><label>الاسم</label><input name="name" value="{{ old('name') }}" required>@error('name')<div class="err">{{ $message }}</div>@enderror</div>
                <div class="field"><label>البريد الإلكتروني</label><input type="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="err">{{ $message }}</div>@enderror</div>
            </div>
            <div class="field"><label>الموضوع</label><input name="subject" value="{{ old('subject') }}" required>@error('subject')<div class="err">{{ $message }}</div>@enderror</div>
            <div class="field"><label>الرسالة</label><textarea name="message" rows="6" required>{{ old('message') }}</textarea>@error('message')<div class="err">{{ $message }}</div>@enderror</div>
            <button class="btn" type="submit">إرسال الرسالة</button>
        </form>
        <aside>
            <div class="panel">
                <h3 style="margin-bottom:10px">بيانات التواصل</h3>
                <ul class="contact-info">
                    @if(setting('contact_email'))<li><strong>البريد الإلكتروني</strong>{{ setting('contact_email') }}</li>@endif
                    @if(setting('contact_phone'))<li><strong>الهاتف</strong><span dir="ltr">{{ setting('contact_phone') }}</span></li>@endif
                    @if(setting('contact_address'))<li><strong>العنوان</strong>{{ setting('contact_address') }}</li>@endif
                </ul>
            </div>
        </aside>
    </div>
</div>
@endsection
