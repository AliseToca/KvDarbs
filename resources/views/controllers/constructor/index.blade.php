@extends('layouts.main', [
    'stylesheet' => 'resources/assets/scss/controllers/text.scss',
])

@section('content')
    <h1>{{ $page->name }}</h1>

    {!! $blocks !!}
@endsection
