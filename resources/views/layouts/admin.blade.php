<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'لوحة التحكم') | {{ setting('site_name', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    @stack('head')
</head>
<body>
@php
    $u = auth()->user();
    $pendingArticles = $u->can('articles.publish') ? \App\Models\Article::where('status', 'pending')->count() : 0;
    $pendingComments = $u->can('comments.moderate') ? \App\Models\Comment::where('status', 'pending')->count() : 0;
    $unreadMessages = $u->can('messages.view') ? \App\Models\ContactMessage::where('is_read', false)->count() : 0;
    $nav = [
        'الرئيسية' => [
            ['admin.dashboard', 'لوحة المعلومات', '▦', null, 'admin.dashboard', 0],
        ],
        'المحتوى' => [
            ['admin.articles.index', 'المواد الصحفية', '✎', 'articles.create', 'admin.articles.*', $pendingArticles],
            ['admin.articles.create', 'كتابة مادة جديدة', '＋', 'articles.create', 'admin.articles.create', 0],
            ['admin.categories.index', 'الأقسام', '☰', 'taxonomy.manage', 'admin.categories.*', 0],
            ['admin.tags.index', 'الوسوم', '#', 'taxonomy.manage', 'admin.tags.*', 0],
            ['admin.pages.index', 'الصفحات الثابتة', '▤', 'pages.manage', 'admin.pages.*', 0],
        ],
        'التفاعل' => [
            ['admin.comments.index', 'التعليقات', '☁', 'comments.moderate', 'admin.comments.*', $pendingComments],
            ['admin.messages.index', 'رسائل التواصل', '✉', 'messages.view', 'admin.messages.*', $unreadMessages],
            ['admin.subscribers.index', 'المشتركون', '☺', 'subscribers.manage', 'admin.subscribers.*', 0],
            ['admin.ads.index', 'الإعلانات', '◈', 'ads.manage', 'admin.ads.*', 0],
        ],
        'النظام' => [
            ['admin.users.index', 'المستخدمون', '☻', 'users.manage', 'admin.users.*', 0],
            ['admin.roles.index', 'الأدوار والصلاحيات', '⚿', 'roles.manage', 'admin.roles.*', 0],
            ['admin.settings', 'إعدادات الموقع', '⚙', 'settings.manage', 'admin.settings', 0],
            ['admin.logs', 'سجل النشاط', '⌚', 'logs.view', 'admin.logs', 0],
        ],
    ];
@endphp
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="side-brand">
            <span class="mark">{{ mb_substr(setting('site_name', config('app.name')), 0, 1) }}</span>
            <span><b>{{ setting('site_name', config('app.name')) }}</b><small>غرفة الأخبار</small></span>
        </a>
        @foreach($nav as $group => $items)
            @php($visible = collect($items)->filter(fn ($i) => ! $i[3] || $u->can($i[3])))
            @if($visible->isNotEmpty())
                <div class="side-group">{{ $group }}</div>
                @foreach($visible as [$route, $label, $icon, $perm, $pattern, $count])
                    <a href="{{ route($route) }}" class="side-link {{ request()->routeIs($pattern) && ! ($route === 'admin.articles.index' && request()->routeIs('admin.articles.create')) ? 'active' : '' }}">
                        <span class="ico">{{ $icon }}</span>{{ $label }}
                        @if($count > 0)<span class="count">{{ $count }}</span>@endif
                    </a>
                @endforeach
            @endif
        @endforeach
        <div class="side-group">الموقع</div>
        <a href="{{ route('home') }}" target="_blank" class="side-link"><span class="ico">↗</span>زيارة الموقع</a>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="left">
                <button class="burger" id="burger" type="button" aria-label="القائمة">☰</button>
                <strong style="color:var(--ink)">@yield('title', 'لوحة التحكم')</strong>
            </div>
            <div class="user-chip">
                <a href="{{ route('admin.profile') }}" style="display:flex;align-items:center;gap:10px">
                    <img src="{{ $u->avatar_url }}" alt="">
                    <span><span class="n">{{ $u->name }}</span><br><small>{{ $u->role?->title }}</small></span>
                </a>
                <form action="{{ route('admin.logout') }}" method="post">@csrf<button class="btn btn-ghost btn-sm" type="submit">خروج</button></form>
            </div>
        </header>

        <div class="content">
            @if(session('success'))<div class="flash ok">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="flash err">{{ session('error') }}</div>@endif
            @if($errors->any())
                <div class="flash err">يرجى تصحيح الأخطاء التالية:
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
<script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>
</body>
</html>
