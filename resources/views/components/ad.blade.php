@props(['position'])
@php($ad = ad_slot($position))
@if($ad)
    <div class="ad-slot">
        <span class="ad-label">إعلان</span>
        @if($ad->image)
            @if($ad->url)<a href="{{ route('ad.click', $ad) }}" target="_blank" rel="sponsored noopener"><img src="{{ upload_url($ad->image) }}" alt="{{ $ad->title }}"></a>
            @else<img src="{{ upload_url($ad->image) }}" alt="{{ $ad->title }}">@endif
        @elseif($ad->code)
            {!! $ad->code !!}
        @endif
    </div>
@endif
