@extends('layouts.blank', [
    'stylesheet' => 'resources/assets/scss/controllers/ui-library/index.scss',
    'script' => 'resources/assets/js/controllers/ui-library/index.ts',
])

@section('content')
    <div class="ui-library">
        <div class="container">
            <nav data-contents>
                <div class="ui-section">
                    <ul>
                        <li><a href="/ui-library">Style guide</a>
                            <ul class="contents">
                                @foreach ($content as $name => $item)
                                    <li><a href="#{{ $name }}">{{ $item->getData()['title'] ?? $name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li><a href="/ui-library/components">Components</a></li>
                    </ul>
                </div>
            </nav>

            <div class="content">
                @foreach ($content as $name => $item)
                    <section class="ui-section" id="{{ $name }}">
                        <h2 class="ui-h2">{{ $item->getData()['title'] ?? $name }}</h2>
                        {{ $item }}
                    </section>
                @endforeach
            </div>
        </div>
    </div>
@endsection
