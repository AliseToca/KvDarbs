@props([
    'class' => '',
    'size' => 'md',
    'text' => '',
    'icon' => null,
    'iconStart' => null,
    'iconSize' => null,
    'disabled' => false,
    'role' => 'button',
    'href' => '',
    'target' => null,
    'type' => 'button',
    'xtext' => '',
    'xhref' => '',
    'xclass' => '',
    'xdisabled' => '',
])

@php
    $tag = !empty($href) ? 'a' : 'button';
    $tag = !empty($xhref) ? 'a' : $tag;
    $disabledAttribute = $disabled ? 'disabled' : '';
    $icononly = ($icon || $iconStart) && empty($text) && empty($xtext) ? 'icon-only' : '';
    $size = $size ? $size : '';
@endphp

<{{ $tag }}
    {{ $attributes->merge([
        'class' => "button $class $size $icononly",
        'type' => $tag === 'button' ? $type : null,
        'href' => $tag === 'a' ? $href : null,
        'role' => $tag === 'a' ? 'link' : 'button',
        'target' => $target ? $target : null,
        'disabled' => $disabled ? 'disabled' : null,
    ]) }}
    @if (!empty($xhref)) x-bind:href="{{ $xhref }}" @endif
    @if (!empty($xclass)) :class="{{ $xclass }}" @endif
    @if (!empty($xdisabled)) :disabled="{{ $xdisabled }}" @endif
>
    @if ($iconStart)
        <div class="icon">
            <x-svg :icon="$iconStart" :size="$iconSize" />
        </div>
    @endif

    @if (!empty($text))
        <span>{!! html_entity_decode($text) !!}</span>
    @endif

    @if (!empty($xtext))
        <span x-text="{{ $xtext }}"></span>
    @endif

    @if (!empty(trim($slot)))
        <span>{{ $slot }}</span>
    @endif

    @if ($icon)
        <div class="icon">
            <x-svg :icon="$icon" :size="$iconSize" />
        </div>
    @endif

    </{{ $tag }}>
