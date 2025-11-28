@php

    $stylesheets = ['resources/assets/scss/abovethefold.scss', 'resources/assets/scss/fonts.scss', 'resources/assets/scss/style.scss'];

    if (isset($stylesheet)) {
        array_push($stylesheets, $stylesheet);
    }

@endphp

@vite($stylesheets)
