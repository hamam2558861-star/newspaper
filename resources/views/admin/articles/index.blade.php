@extends('layouts.admin')
@section('title', 'المواد الصحفية')

@section('content')
<div class="page-head">
    <div><h1>المواد الصحفية</h1><p>{{ $seeAll ? 'جميع مواد غرفة الأخبار' : 'موادك الشخصية' }}</p></div>
    <a class="btn btn-red" href="{{ route('admin.articles.create') }}">＋ مادة جديدة</a>
</div>

<div class="tabs">
    @php($cur = request('status'))
    <a href="{{ route('admin.articles.index') }}" class="{{ ! $cur ? 'on' : '' }}">الكل</a>
    @foreach(['draft' => 'مسودات', 'pending' => 'بانتظار المراجعة', 'published' => 'منشورة', 'scheduled' => 'مجدولة', 'rejected' => 'مرفوضة', 'archived' => 'مؤرشفة'] as $k => $v)
        <a href="{{ route('admin.articles.index', array_merge(request()->except('page'), ['status' => $k])) }}" class="{{ $cur === $k ? 'on' : '' }}">{{ $v }}</a>
    @endforeach
</div>

<div class="card">
    <form class="filters" method="get">
        @if($cur)<input type="hidden" name="status" value="{{ $cur }}">@endif
        <input type="search" name="q" value="{{ request('q') }}" placeholder="بحث بالعنوان...">
        <select name="category"><option value="">كل الأقسام</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach</select>
        <select name="type"><option value="">كل الأنواع</option>@foreach(config('newsroom.article_types') as $k => $v)<option value="{{ $k }}" @selected(request('type') === $k)>{{ $v }}</option>@endforeach</select>
        @if($seeAll)<label class="check" style="padding:0"><input type="checkbox" name="mine" value="1" @checked(request('mine')) onchange="this.form.submit()"> موادي فقط</label>@endif
        <button class="btn btn-sm" type="submit">تصفية</button>
    </form>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead><tr><th></th><th>العنوان</th><th>القسم</th><th>الكاتب</th><th>الحالة</th><th>التاريخ</th><th>مشاهدات</th><th></th></tr></thead>
            <tbody>
            @forelse($articles as $a)
                <tr>
                    <td>@if($a->image_url)<img class="thumb-sm" src="{{ $a->image_url }}" alt="" onerror="this.remove()">@endif</td>
                    <td style="max-width:380px">
                        <a class="title" href="{{ route('admin.articles.edit', $a) }}">{{ $a->title }}</a>
                        <div class="sub">
                            {{ $a->type_label }}
                            @if($a->is_breaking)· <span style="color:var(--red);font-weight:700">عاجل</span>@endif
                            @if($a->is_featured)· مميز@endif
                        </div>
                    </td>
                    <td>{{ $a->category->name }}</td>
                    <td>{{ $a->author->name }}</td>
                    <td>@include('admin.articles._status', ['a' => $a])</td>
                    <td><span class="sub">{{ ($a->published_at ?? $a->updated_at)->format('Y/m/d H:i') }}</span></td>
                    <td>{{ number_format($a->views) }}</td>
                    <td>
                        <div class="actions">
                            @can('update', $a)<a class="btn btn-ghost btn-sm" href="{{ route('admin.articles.edit', $a) }}">تحرير</a>@endcan
                            <a class="btn btn-ghost btn-sm" href="{{ $a->url() }}" target="_blank">عرض</a>
                            @can('delete', $a)
                            <form method="post" action="{{ route('admin.articles.destroy', $a) }}" data-confirm="حذف هذه المادة؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty">لا توجد مواد مطابقة.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $articles->links() }}
</div>
@endsection
