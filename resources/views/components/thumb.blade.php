@props(['a', 'ratio' => '16x9', 'playSm' => false])
<div {{ $attributes->class(['thumb', 'r' . $ratio]) }} style="--c: {{ $a->category->color ?? '#0f2a47' }}">
    @if($a->image_url)
        <img src="{{ $a->image_url }}" alt="{{ $a->title }}" loading="lazy" onerror="this.remove()">
    @endif
    @if($a->type === 'video')<span class="play {{ $playSm ? 'sm' : '' }}">▶</span>@endif
</div>
