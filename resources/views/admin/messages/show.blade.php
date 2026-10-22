@extends('layouts.admin')
@section('title', $message->subject)

@section('content')
<div class="page-head"><h1>{{ $message->subject }}</h1><a class="btn btn-ghost" href="{{ route('admin.messages.index') }}">← الرسائل</a></div>
<div class="card" style="max-width:820px">
    <div class="card-h"><div><strong>{{ $message->name }}</strong> <span class="sub" style="color:var(--muted)">&lt;{{ $message->email }}&gt;</span></div><span class="sub" style="color:var(--muted)">{{ $message->created_at->format('Y/m/d H:i') }}</span></div>
    <div class="card-b"><div class="msg-body">{{ $message->message }}</div></div>
    <div class="sticky-actions">
        <a class="btn" href="mailto:{{ $message->email }}?subject={{ rawurlencode('رد: ' . $message->subject) }}">الرد عبر البريد</a>
        <form method="post" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="حذف الرسالة؟">@csrf @method('DELETE')<button class="btn btn-danger">حذف</button></form>
    </div>
</div>
@endsection
