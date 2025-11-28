@props([
    'mediaData' => [],
    'srcset' => null,
    'src' => null,
    'alt' => '',
    'class' => '',
    'sizes' => null,
    'context' => null,
    'lazy' => true,
    'caption' => '',
    'width' => null,
    'height' => null,
])

@php
    $figureId = $caption ? 'figure-' . uniqid() : null;
    $loading = $lazy ? 'lazy' : 'eager';
    $contextSizes = [
        'hero' => '100vw',
        'card' => '(max-width: 30rem) 50vw, (max-width: 48rem) 33vw, 25vw',
        'thumbnail' => '(max-width: 30rem) 25vw, (max-width: 48rem) 20vw, 15vw',
        'gallery' => '(max-width: 30rem) 100vw, (max-width: 48rem) 50vw, 33vw',
        'content' => '(max-width: 48rem) 100vw, 50vw',
        'default' => '(max-width: 30rem) 100vw, (max-width: 48rem) 50vw, 33vw',
    ];
    $src = $mediaData['src'] ?? $src;
    $srcset = $mediaData['srcset'] ?? $srcset;
    $width = $mediaData['width'] ?? $width;
    $height = $mediaData['height'] ?? $height;
@endphp

<figure {{ $attributes->merge(['class' => trim('picture ' . ($class ?? ''))]) }}>
    <picture>
        @if ($srcset)
            <source
                srcset="{{ $srcset }}"
                type="image/webp"
                sizes="{{ $context ? $contextSizes[$context] : $sizes ?? $contextSizes['default'] }}"
            >
        @endif

        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            loading="{{ $loading }}"
            @if ($width) width="{{ $width }}" @endif
            @if ($height) height="{{ $height }}" @endif
            @if ($caption) aria-describedby="{{ $figureId }}-caption" @endif
            style="opacity: 0;"
        >
    </picture>

    @if ($caption)
        <figcaption id="{{ $figureId }}-caption">
            {{ $caption }}
        </figcaption>
    @endif
</figure>
