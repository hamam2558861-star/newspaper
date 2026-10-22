@extends('layouts.site')
@section('title', $article->meta_title ?: $article->title)
@section('description', $article->meta_description ?: $article->excerpt)
@section('image', $article->image_url)
@section('og_type', 'article')

@section('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $article->title,
    'description' => $article->excerpt,
    'image' => $article->image_url ? [$article->image_url] : [],
    'datePublished' => $article->published_at?->toIso8601String(),
    'dateModified' => $article->updated_at->toIso8601String(),
    'author' => ['@type' => 'Person', 'name' => $article->author->name],
    'publisher' => ['@type' => 'Organization', 'name' => setting('site_name', config('app.name'))],
    'mainEntityOfPage' => $article->url(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
</script>
@endsection

@section('content')
@php($c = $article->category->color ?? '#c8102e')
<div class="container">
    <div class="layout">
        <article style="--c: {{ $c }}">
            @if($isPreview)<div class="preview-bar">معاينة: هذه المادة غير منشورة للجمهور بعد (الحالة: {{ $article->status_label }}).</div>@endif

            <nav class="breadcrumbs" aria-label="مسار التصفح">
                <a href="{{ route('home') }}">الرئيسية</a> ›
                @if($article->category->parent)<a href="{{ $article->category->parent->url() }}">{{ $article->category->parent->name }}</a> ›@endif
                <a href="{{ $article->category->url() }}">{{ $article->category->name }}</a>
            </nav>

            <header class="article-head">
                @if($article->kicker)<div class="kicker">{{ $article->kicker }}</div>@endif
                <h1>{{ $article->title }}</h1>
                @if($article->excerpt)<p class="lead">{{ $article->excerpt }}</p>@endif
            </header>

            <div class="byline">
                <img src="{{ $article->author->avatar_url }}" alt="">
                <div>
                    <div class="who"><a href="{{ route('author.show', $article->author) }}">{{ $article->author->name }}</a></div>
                    <div class="when">
                        @if($article->dateline){{ $article->dateline }} — @endif
                        {{ ar_date($article->published_at ?? $article->created_at, 'j F Y، H:i') }}
                        · {{ $article->reading_minutes }} دقائق قراءة · {{ number_format($article->views) }} مشاهدة
                    </div>
                </div>
                <div class="share">
                    @php($u = urlencode($article->url()))
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $u }}" target="_blank" rel="noopener">فيسبوك</a>
                    <a href="https://x.com/intent/post?url={{ $u }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener">إكس</a>
                    <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . $article->url()) }}" target="_blank" rel="noopener">واتساب</a>
                    <button type="button" data-copy="{{ $article->url() }}">نسخ الرابط</button>
                    <button type="button" onclick="window.print()">طباعة</button>
                </div>
            </div>

            @if($article->video_embed)
                <div class="video-frame"><iframe src="{{ $article->video_embed }}" title="{{ $article->title }}" allowfullscreen loading="lazy"></iframe></div>
            @elseif($article->image_url)
                <figure class="figure">
                    <x-thumb :a="$article" />
                    @if($article->image_caption)<figcaption>{{ $article->image_caption }}</figcaption>@endif
                </figure>
            @endif

            <div class="prose">{!! $article->body !!}</div>

            <x-ad position="article_inline" />

            <div class="article-foot">
                @if($article->source)<p style="color:var(--muted)">المصدر: {{ $article->source }}</p>@endif
                @if($article->tags->isNotEmpty())
                    <div class="tags">@foreach($article->tags as $t)<a href="{{ route('tag.show', $t->slug) }}">{{ $t->name }}</a>@endforeach</div>
                @endif
            </div>

            @if($article->author->bio && $article->type === 'opinion')
            <div class="author-box">
                <img src="{{ $article->author->avatar_url }}" alt="">
                <div><h3 style="font-size:18px">{{ $article->author->name }}</h3><div class="meta" style="margin-bottom:6px">{{ $article->author->job_title }}</div><p style="margin:0">{{ $article->author->bio }}</p></div>
            </div>
            @endif

            {{-- التعليقات --}}
            @if($article->allow_comments && setting('comments_enabled', '1') === '1')
            <section class="comments section" id="comments">
                <h3>التعليقات ({{ $comments->count() }})</h3>
                @foreach($comments as $cm)
                    <div class="comment">
                        <div class="avatar">{{ mb_substr($cm->name, 0, 1) }}</div>
                        <div><span class="who">{{ $cm->name }}</span><span class="when">{{ $cm->created_at->diffForHumans() }}</span><p>{{ $cm->body }}</p></div>
                    </div>
                @endforeach
                <form action="{{ route('article.comment', $article) }}" method="post" style="margin-top:24px" class="panel">
                    @csrf
                    <h4 style="margin-bottom:12px">أضف تعليقك</h4>
                    @if(session('comment_ok'))<div class="flash ok">{{ session('comment_ok') }}</div>@endif
                    <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <div class="form-row">
                        <div class="field"><label>الاسم</label><input name="name" value="{{ old('name') }}" required>@error('name')<div class="err">{{ $message }}</div>@enderror</div>
                        <div class="field"><label>البريد الإلكتروني (لن يُنشر)</label><input type="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="err">{{ $message }}</div>@enderror</div>
                    </div>
                    <div class="field"><label>التعليق</label><textarea name="body" rows="4" required>{{ old('body') }}</textarea>@error('body')<div class="err">{{ $message }}</div>@enderror</div>
                    <button class="btn" type="submit">إرسال التعليق</button>
                </form>
            </section>
            @endif

            @if($related->isNotEmpty())
            <section class="section">
                <div class="sec-head"><h2>مواد ذات صلة</h2></div>
                <div class="grid g2">@foreach($related as $a)<x-card :a="$a" variant="sm" :show-cat="false" />@endforeach</div>
            </section>
            @endif
        </article>

        <aside class="sidebar">
            <x-ad position="sidebar_top" />
            <div class="most-read">
                <div class="sec-head"><h2>الأكثر قراءة</h2></div>
                <ol>@foreach($mostRead as $a)<li><a href="{{ $a->url() }}">{{ $a->title }}</a></li>@endforeach</ol>
            </div>
            <x-ad position="sidebar_bottom" />
        </aside>
    </div>
</div>
@endsection
