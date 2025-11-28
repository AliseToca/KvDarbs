<section class="ui-section" id="sliders">
    <h2 class="ui-h2">Sliders</h2>
    <p>Slider functionality achieved using <a href="https://github.com/ganlanyuan/tiny-slider">TinySlider</a>.</p>

    @php
        $sliderVariants = [
            [
                'title' => 'Horizontal',
                'controls' => true,
                'nav' => true,
                'vertical' => false,
            ],
            [
                'title' => 'Horizontal (controls only)',
                'controls' => true,
                'nav' => false,
                'vertical' => false,
            ],
            [
                'title' => 'Horizontal (nav only)',
                'controls' => false,
                'nav' => true,
                'vertical' => false,
            ],
            [
                'title' => 'Vertical',
                'controls' => true,
                'nav' => true,
                'vertical' => true,
            ],
        ];
    @endphp

    @foreach ($sliderVariants as $variant)
        @php
            $props = collect($variant)->except('title')->filter()->keys()->implode(' ');
            $code = "<x-slider {$props}>
                    <div>First</div>
                    <div>Second</div>
                    <div>Third</div>
                </x-slider>";
        @endphp

        <h2 class="ui-h3">{{ $variant['title'] }}</h3>
            <div class="instructions">
                <div class="grid-container slider-grid">
                    <div class="column-7">
                        <x-slider
                            :controls="$variant['controls']"
                            :nav="$variant['nav']"
                            :vertical="$variant['vertical']"
                        >
                            @foreach ($testimonials as $testimonial)
                                <div>
                                    @include('ui-library.components.part.demo-card', [
                                        'testimonial' => $testimonial,
                                    ])
                                </div>
                            @endforeach
                        </x-slider>
                    </div>
                    <div class="column-5">

                        <x-code-example language="blade">{!! $code !!}</x-code-example>
                    </div>
                </div>
            </div>
    @endforeach

</section>
