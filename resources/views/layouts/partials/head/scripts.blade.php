@php
    $entries = [];
    if (isset($scripts) && is_array($scripts)) {
        $entries = array_merge($scripts, $entries);
    } elseif (isset($script)) {
        $entries[] = $script;
    }
    array_push($entries, 'resources/assets/js/app.ts');
@endphp

@vite($entries)
