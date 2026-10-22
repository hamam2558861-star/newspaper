@extends('layouts.site')
@section('title', $page->title)

@section('content')
<div class="container">
    <div class="static-page">
        <div class="page-title"><h1>{{ $page->title }}</h1></div>
        <div class="prose">{!! $page->body !!}</div>
    </div>
</div>
@endsection
