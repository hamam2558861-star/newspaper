@extends('layouts.admin')
@section('title', 'الإعلانات')

@section('content')
<div class="page-head"><div><h1>الإعلانات</h1><p>إدارة المساحات الإعلانية وفترات الحملات.</p></div><a class="btn btn-red" href="{{ route('admin.ads.create') }}">＋ إعلان جديد</a></div>
<div class="card tbl-wrap"><table class="tbl">
    <thead><tr><th>الإعلان</th><th>الموضع</th><th>الفترة</th><th>النقرات</th><th>الحالة</th><th></th></tr></thead>
    <tbody>
    @forelse($ads as $ad)
        @php($live = $ad->is_active && (! $ad->starts_at || $ad->starts_at->lte(now())) && (! $ad->ends_at || $ad->ends_at->gte(now())))
        <tr>
            <td><strong>{{ $ad->title }}</strong></td>
            <td>{{ config('newsroom.ad_positions')[$ad->position] ?? $ad->position }}</td>
            <td><span class="sub">{{ $ad->starts_at?->format('Y/m/d') ?? '—' }} ← {{ $ad->ends_at?->format('Y/m/d') ?? 'مفتوح' }}</span></td>
            <td>{{ number_format($ad->clicks) }}</td>
            <td><span class="badge {{ $live ? 'b-ok' : '' }}">{{ $live ? 'يعمل الآن' : ($ad->is_active ? 'خارج الفترة' : 'متوقف') }}</span></td>
            <td><div class="actions">
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.ads.edit', $ad) }}">تعديل</a>
                <form method="post" action="{{ route('admin.ads.destroy', $ad) }}" data-confirm="حذف الإعلان؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
            </div></td>
        </tr>
    @empty<tr><td colspan="6"><div class="empty">لا توجد إعلانات.</div></td></tr>@endforelse
    </tbody>
</table>{{ $ads->links() }}</div>
@endsection
