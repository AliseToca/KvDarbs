<title>{{ $metaData->name }}</title>
<meta name="description" content="{{ $metaData->description }}">

<meta property="og:title" content="{{ $metaData->name }}">
<meta property="og:description" content="{{ $metaData->description }}">
<meta property="og:url" content="{{ url($metaData->path) }}">
<meta property="og:type" content="website">
@if ($metaData->image)
    <meta property="og:image" content="{{ $metaData->image }}">
@endif
@if ($metaData->keywords)
    <meta name="keywords" content="{{ join(', ', $metaData->keywords) }}" />
@endif
