@props([
    'class' => '',
    'vertical' => false,
    'controls' => false,
    'nav' => false,
    'dataSlider' => 'horizontal',
])

<div
    class="slider {!! $vertical ? 'vertical' : 'horizontal' !!} {!! $class !!}"
    data-slider="{!! $vertical ? 'vertical' : $dataSlider !!}"
    {{ $attributes }}
>

    <div data-slider-container>
        {{ $slot }}
    </div>

    @if ($nav || $controls)

        <div class="slider-controls" data-slider-controls>
            @if ($controls)
                <x-button
                    data-controls="prev"
                    title="Previous"
                    size="md"
                    icon="chevron-{{ $vertical ? 'up' : 'left' }}"
                />
                <x-button
                    data-controls="next"
                    title="Next"
                    size="md"
                    icon="chevron-{{ $vertical ? 'down' : 'right' }}"
                />
            @endif

            @if ($nav)
                <div
                    class="slider-nav tns-nav"
                    data-slider-nav
                    tabindex="-1"
                ></div>
            @endif
        </div>
    @endif
</div>
