<div class="container">
    @foreach ($languageMenu as $locale => $link)
        <a href="{{ $link }}">{{ $locale }}</a>
    @endforeach
</div>
