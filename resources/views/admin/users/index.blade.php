@extends('layouts.admin')
@section('title', 'المستخدمون')

@section('content')
<div class="page-head"><div><h1>المستخدمون</h1><p>حسابات فريق العمل وأدوارهم الوظيفية.</p></div><a class="btn btn-red" href="{{ route('admin.users.create') }}">＋ مستخدم جديد</a></div>
<div class="card">
    <form class="filters" method="get">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="بحث بالاسم أو البريد...">
        <select name="role"><option value="">كل الأدوار</option>@foreach($roles as $r)<option value="{{ $r->id }}" @selected(request('role') == $r->id)>{{ $r->title }}</option>@endforeach</select>
        <button class="btn btn-sm">تصفية</button>
    </form>
    <div class="tbl-wrap"><table class="tbl">
        <thead><tr><th>المستخدم</th><th>الدور الوظيفي</th><th>المواد</th><th>آخر دخول</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td><div style="display:flex;align-items:center;gap:10px"><img class="thumb-sm" style="width:38px;height:38px;border-radius:50%" src="{{ $u->avatar_url }}" alt=""><div><strong>{{ $u->name }}</strong><div class="sub">{{ $u->email }}</div></div></div></td>
                <td><span class="badge b-info">{{ $u->role?->title ?? 'بدون دور' }}</span><div class="sub">{{ $u->job_title }}</div></td>
                <td>{{ $u->articles_count }}</td>
                <td><span class="sub">{{ $u->last_login_at?->diffForHumans() ?? 'لم يدخل بعد' }}</span></td>
                <td><span class="badge {{ $u->is_active ? 'b-ok' : 'b-err' }}">{{ $u->is_active ? 'فعّال' : 'معطّل' }}</span></td>
                <td><div class="actions">
                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.users.edit', $u) }}">تعديل</a>
                    @if($u->id !== auth()->id())
                    <form method="post" action="{{ route('admin.users.destroy', $u) }}" data-confirm="حذف المستخدم؟">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">حذف</button></form>
                    @endif
                </div></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $users->links() }}
</div>
@endsection
