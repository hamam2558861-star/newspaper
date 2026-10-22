@props(['a', 'variant' => 'default', 'excerpt' => false, 'showCat' => true])
@php($c = $a->category->color ?? '#c8102e')
@if($variant === 'row')
<a href="{{ $a->url() }}" class="card-row" style="--c: {{ $c }}">
    <x-thumb :a="$a" ratio="4x3" :play-sm="true" />
    <div>
        <h3 class="ttl">{{ $a->title }}</h3>
        <div class="meta">
            @if($showCat && $a->category)<span style="color:{{ $c }};font-weight:700">{{ $a->category->name }}</span>@endif
            <span>{{ $a->published_at?->diffForHumans() }}</span>
        </div>
    </div>
</a>
@elseif($variant === 'list')
<a href="{{ $a->url() }}" class="card-list" style="--c: {{ $c }}">
    <x-thumb :a="$a" ratio="16x9" />
    <div>
        @if($a->category)<span class="badge">{{ $a->category->name }}</span>@endif
        <h3 class="ttl">{{ $a->title }}</h3>
        <p>{{ $a->excerpt }}</p>
        <div class="meta"><span>{{ $a->type_label }}</span><span>{{ ar_date($a->published_at, 'j F Y') }}</span><span>{{ number_format($a->views) }} مشاهدة</span></div>
    </div>
</a>
@else
<a href="{{ $a->url() }}" class="card {{ $variant === 'sm' ? 'sm' : '' }}" style="--c: {{ $c }}">
    <x-thumb :a="$a" />
    @if($a->kicker)<div class="kicker">{{ $a->kicker }}</div>@elseif($showCat && $a->category)<div class="kicker">{{ $a->category->name }}</div>@endif
    <h3 class="ttl">{{ $a->title }}</h3>
    @if($excerpt)<p>{{ $a->excerpt }}</p>@endif
    <div class="meta" style="margin-top:6px"><span>{{ $a->published_at?->diffForHumans() }}</span></div>
</a>
@endif
