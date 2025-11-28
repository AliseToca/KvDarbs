@props([
    'icon' => $icon ?? null,
    'class' => $class ?? null,
    'size' => $size ?? null,
    'width' => $width ?? null,
    'height' => $height ?? null,
])

<svg
    xmlns="http://www.w3.org/2000/svg"
    @isset($class) class="{{ $class ?? '' }}" @endisset
    @if ($size || $width) width="{{ $width ?? $size }}" @endif
    @if ($size || $height) height="{{ $height ?? $size }}" @endif
    {{ $attributes }}
>
    <use href="{{ $svgSpriteUrl }}#{{ $icon }}" />
</svg>
