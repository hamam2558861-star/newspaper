<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>تسجيل الدخول | {{ setting('site_name', config('app.name')) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="login-wrap">
    <div class="login-art">
        <div style="font-size:22px;font-weight:900">{{ setting('site_name', config('app.name')) }}</div>
        <div>
            <h1>غرفة الأخبار الرقمية<br>كل ما يحتاجه فريق التحرير في مكان واحد.</h1>
            <ul>
                <li>✓ سير عمل تحريري: مسودة ← مراجعة ← نشر</li>
                <li>✓ صلاحيات دقيقة حسب المسمى الوظيفي</li>
                <li>✓ جدولة النشر والأخبار العاجلة</li>
                <li>✓ إدارة التعليقات والإعلانات والنشرة البريدية</li>
            </ul>
        </div>
        <small style="opacity:.6">© {{ date('Y') }} جميع الحقوق محفوظة</small>
    </div>
    <div class="login-form">
        <form method="post" action="{{ route('admin.login.submit') }}">
            @csrf
            <h2>تسجيل الدخول</h2>
            <p style="color:var(--muted);margin:0 0 24px">أدخل بيانات حسابك للوصول إلى لوحة التحكم.</p>
            @if($errors->any())<div class="flash err">{{ $errors->first() }}</div>@endif
            <x-a.input name="email" label="البريد الإلكتروني" type="email" required autofocus dir="ltr" style="text-align:right" />
            <x-a.input name="password" label="كلمة المرور" type="password" required dir="ltr" style="text-align:right" />
            <label class="check"><input type="checkbox" name="remember" value="1"> تذكّرني على هذا الجهاز</label>
            <button class="btn btn-red" type="submit" style="width:100%;justify-content:center;margin-top:10px;padding:12px">دخول</button>
            <p style="text-align:center;margin-top:20px"><a href="{{ route('home') }}" style="color:var(--muted)">← العودة إلى الموقع</a></p>
        </form>
    </div>
</div>
</body>
</html>
