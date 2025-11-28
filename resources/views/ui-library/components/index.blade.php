@extends('layouts.blank', [
    'stylesheet' => 'resources/assets/scss/controllers/ui-library/index.scss',
    'scripts' => ['resources/assets/js/controllers/ui-library/index.ts'],
])

@section('content')
    <div class="ui-library">
        <div class="container">
            @include('ui-library.components.sections.navigation')
            <div class="content">
                @include('ui-library.components.sections.grid')
                @include('ui-library.components.sections.buttons')
                @include('ui-library.components.sections.form-fields')
                @include('ui-library.components.sections.accordion')
                @include('ui-library.components.sections.modals')
                @include('ui-library.components.sections.slider')
                @include('ui-library.components.sections.tabs')
                @include('ui-library.components.sections.picture')
            </div>
        </div>
    </div>
@endsection
