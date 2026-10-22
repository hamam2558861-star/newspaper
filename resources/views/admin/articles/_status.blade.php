@php
    $cls = match (true) {
        $a->isScheduled() => 'b-info',
        $a->status === 'published' => 'b-ok',
        $a->status === 'pending' => 'b-warn',
        $a->status === 'rejected' => 'b-err',
        default => '',
    };
@endphp
<span class="badge {{ $cls }}">{{ $a->status_label }}</span>
