<section class="ui-section" id="tabs">
    <h2 class="ui-h2">Tabs</h2>

    <p>The tabs component provides accessible, keyboard-navigable tabbed panels for organizing content. Tabs are implemented using Blade components and a TypeScript controller for interactive behavior.</p>
    <ul>
        <li>Tabs are rendered with <code>&lt;x-tabs :tabs="$tabs"&gt;</code> and each tab panel is defined via <code>&lt;x-slot name="panelN"&gt;</code>.</li>
        <li>Keyboard navigation is supported: users can use Tab and Arrow keys to switch between tabs.</li>
        <li>ARIA attributes (<code>aria-selected</code>, <code>aria-controls</code>, <code>aria-hidden</code>) are set for accessibility.</li>
        <li>Responsive layouts are supported via SCSS, including vertical sidebar tabs.</li>
        <li>Tab state is managed by the <code>Tabs</code> TypeScript class (<code>resources/assets/js/modules/tabs.ts</code>).</li>
        <li>To initialize tabs, ensure the JS controller is loaded and markup includes <code>data-tabbed-content</code> and <code>data-tab</code> attributes.</li>
    </ul>
    <br>

    <h2 class="ui-h3">Blade Component Example</h3>

        <x-code-example language="blade">
            @verbatim
                @php
                    $tabs = [
                        0 => 'Tab title 1',
                        1 => 'Tab title 2',
                        2 => 'Tab title 3',
                    ];
                @endphp

                <x-tabs :tabs="$tabs">
                    @foreach ($tabs as $index => $panel)
                        <x-slot :name="'panel' . $index">
                            <p>Panel content for {{ $tabs[$index] }}</p>
                        </x-slot>
                    @endforeach
                </x-tabs>
            @endverbatim
        </x-code-example>

        <h2 class="ui-h3">Demo</h3>
            <div class="instructions">

                @php
                    $tabs = collect($testimonials)->take(4)->pluck('name')->toArray();
                @endphp

                <x-tabs :tabs="$tabs">
                    @foreach ($testimonials as $index => $testimonial)
                        @if ($index < 4)
                            <x-slot :name="'panel' . $index">
                                @include('ui-library.components.part.demo-card', [
                                    'testimonial' => $testimonial,
                                ])
                            </x-slot>
                        @endif
                    @endforeach
                </x-tabs>
            </div>
</section>
