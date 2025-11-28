@extends('layouts.main', [
    'stylesheet' => 'resources/assets/scss/controllers/language.scss',
    'script' => 'resources/assets/js/controllers/language/index.ts',
])

@section('content')
    <h1>{{ $page->name }}</h1>

    {!! $blocks !!}
@endsection
