<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $siteName = setting('site_name', config('app.name'));
        $pageTitle = trim($__env->yieldContent('title'));
        $pageDesc = trim($__env->yieldContent('description')) ?: setting('site_description');
        $pageImage = trim($__env->yieldContent('image'));
    @endphp
    <title>{{ $pageTitle ? $pageTitle . ' | ' . $siteName : $siteName . ' - ' . setting('site_tagline') }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($pageDesc), 200) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle ?: $siteName }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($pageDesc), 200) }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="ar_AR">
    @if($pageImage)<meta property="og:image" content="{{ $pageImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#0b1f3a">
    @if(setting('favicon'))<link rel="icon" href="{{ upload_url(setting('favicon')) }}">@endif
    <link rel="alternate" type="application/rss+xml" title="{{ $siteName }}" href="{{ route('rss') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
    <script>try{var t=localStorage.getItem('theme');if(t)document.documentElement.setAttribute('data-theme',t)}catch(e){}</script>
    @yield('head')
    {!! setting('head_code') !!}
</head>
<body>
@php($social = array_filter(['فيسبوك' => setting('facebook'), 'إكس' => setting('x_twitter'), 'إنستغرام' => setting('instagram'), 'يوتيوب' => setting('youtube'), 'تيليغرام' => setting('telegram')]))

<div class="topbar">
    <div class="container">
        <div class="links">
            <span>{{ ar_date(now(), 'l، j F Y') }}</span>
            <a href="{{ route('latest') }}">آخر الأخبار</a>
            <a href="{{ route('page.show', 'about') }}">من نحن</a>
            <a href="{{ route('contact') }}">اتصل بنا</a>
        </div>
        <div class="tools">
            <span class="social">
                @foreach($social as $label => $link)<a href="{{ $link }}" target="_blank" rel="noopener" style="border:0;padding:0">{{ $label }}</a>@endforeach
            </span>
            <a href="{{ route('rss') }}" title="RSS">RSS</a>
            <button class="icon-btn" id="theme-toggle" type="button" aria-label="تبديل الوضع الداكن">◐</button>
        </div>
    </div>
</div>

<header class="masthead">
    <div class="container">
        <a href="{{ route('home') }}" class="brand">
            @if(setting('logo'))
                <img src="{{ upload_url(setting('logo')) }}" alt="{{ $siteName }}">
            @else
                <span class="brand-mark">{{ mb_substr($siteName, 0, 1) }}</span>
                <span><span class="brand-name">{{ $siteName }}</span><br><span class="brand-tag">{{ setting('site_tagline') }}</span></span>
            @endif
        </a>
        <div class="head-actions">
            <form class="search-form" action="{{ route('search') }}" method="get" role="search">
                <input type="search" name="q" placeholder="ابحث في الأخبار..." value="{{ request('q') }}" aria-label="بحث">
                <button type="submit">بحث</button>
            </form>
            <button class="menu-toggle" id="menu-toggle" type="button" aria-label="القائمة">☰</button>
        </div>
    </div>
</header>

@if($headerAd = ad_slot('header'))
<div class="header-ad"><x-ad position="header" /></div>
@endif

<nav class="mainnav" id="mainnav" aria-label="الأقسام">
    <div class="container">
        <ul class="nav">
            <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="{{ request()->routeIs('latest') ? 'active' : '' }}"><a href="{{ route('latest') }}">آخر الأخبار</a></li>
            @foreach($menuCategories ?? [] as $cat)
                <li class="{{ request()->is('section/' . $cat->slug) ? 'active' : '' }}" style="--c: {{ $cat->color }}">
                    <a href="{{ $cat->url() }}">{{ $cat->name }}</a>
                    @if($cat->children->isNotEmpty())
                        <div class="dropdown">
                            @foreach($cat->children as $child)<a href="{{ $child->url() }}">{{ $child->name }}</a>@endforeach
                        </div>
                    @endif
                </li>
            @endforeach
            <li class="{{ request()->routeIs('opinions') ? 'active' : '' }}"><a href="{{ route('opinions') }}">رأي</a></li>
            <li class="{{ request()->routeIs('videos') ? 'active' : '' }}"><a href="{{ route('videos') }}">فيديو</a></li>
        </ul>
    </div>
</nav>

@if(($breakingNews ?? collect())->isNotEmpty())
<div class="ticker">
    <div class="container">
        <span class="ticker-label"><i class="pulse"></i><span class="t">عاجل</span></span>
        <div class="ticker-track">
            @foreach($breakingNews as $b)
                <a href="{{ $b->url() }}"><span class="ticker-time">{{ $b->published_at->format('H:i') }}</span>{{ $b->title }}</a>
            @endforeach
        </div>
    </div>
</div>
@endif

<main>
    @yield('content')
</main>

@if($footerAd = ad_slot('footer'))
<div class="container" style="padding-bottom:30px"><x-ad position="footer" /></div>
@endif

<section class="newsletter">
    <div class="container">
        <div>
            <h3>نشرتنا البريدية</h3>
            <p>أهم أخبار اليوم تصلك إلى بريدك الإلكتروني.</p>
        </div>
        <div style="flex:1;max-width:520px;min-width:280px">
            <form action="{{ route('subscribe') }}" method="post" style="max-width:none">
                @csrf
                <input type="email" name="email" placeholder="بريدك الإلكتروني" required aria-label="البريد الإلكتروني">
                <button class="btn" type="submit">اشترك الآن</button>
            </form>
            @if(session('subscribed'))<div class="flash ok">{{ session('subscribed') }}</div>@endif
            @error('email')<div class="flash err">{{ $message }}</div>@enderror
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="brand"><span class="brand-mark">{{ mb_substr($siteName, 0, 1) }}</span><span class="brand-name">{{ $siteName }}</span></div>
                <p style="margin-top:14px">{{ setting('footer_about') }}</p>
                <div class="social">@foreach($social as $label => $link)<a href="{{ $link }}" target="_blank" rel="noopener">{{ $label }}</a>@endforeach</div>
            </div>
            <div>
                <h4>الأقسام</h4>
                <ul>@foreach($menuCategories ?? [] as $cat)<li><a href="{{ $cat->url() }}">{{ $cat->name }}</a></li>@endforeach</ul>
            </div>
            <div>
                <h4>روابط مهمة</h4>
                <ul>
                    @foreach($footerPages ?? [] as $p)<li><a href="{{ route('page.show', $p->slug) }}">{{ $p->title }}</a></li>@endforeach
                    <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
                    <li><a href="{{ route('rss') }}">خلاصة RSS</a></li>
                </ul>
            </div>
            <div>
                <h4>تواصل معنا</h4>
                <ul>
                    @if(setting('contact_email'))<li>{{ setting('contact_email') }}</li>@endif
                    @if(setting('contact_phone'))<li dir="ltr" style="text-align:right">{{ setting('contact_phone') }}</li>@endif
                    @if(setting('contact_address'))<li>{{ setting('contact_address') }}</li>@endif
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">© {{ date('Y') }} {{ $siteName }} — {{ setting('copyright') }}</div>
</footer>

<script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}"></script>
@stack('scripts')
</body>
</html>
