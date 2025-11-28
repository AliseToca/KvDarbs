<section class="ui-section" id="accordion">
    <h2 class="ui-h2">Accordion</h2>
    <h2 class="ui-h3">Blade component</h3>
        @component('ui-library.components.part.instructions-block')
            <p>
                Use the <code>x-accordion</code> component to create accordion items. The component provides two slots:
            </p>
            <ul>
                <li><code>head</code>: Visible header content</li>
                <li><code>body</code>: Hidden expandable content</li>
            </ul>

            <x-code-example language="blade">
                @verbatim
                    <x-accordion>
                        <x-slot name="head">
                            <span>Accordion title</span>
                        </x-slot>
                        <x-slot name="body">
                            <p>Accordion body</p>
                        </x-slot>
                    </x-accordion>
                @endverbatim
            </x-code-example>
        @endcomponent

        <h2 class="ui-h3">Accordion demo</h3>
            <div class="instructions">

                <x-accordion class="indicator">
                    <x-slot name="head">
                        <span>Accordion title</span>
                    </x-slot>
                    <x-slot name="body">
                        <p> Accordion body </p>
                    </x-slot>
                </x-accordion>

                <x-accordion class="reverse">
                    <x-slot name="head">
                        <span>Test case for a very long accordion title that wraps in multiple lines. Lorem ipsum dolor sit
                            amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet,
                            adipiscing.</span>
                    </x-slot>
                    <x-slot name="body">
                        <p> Accordion body </p>
                    </x-slot>
                </x-accordion>

                <x-accordion>
                    <x-slot name="head">
                        <span>Progressive enhancement</span>
                    </x-slot>
                    <x-slot name="body">
                        @component('ui-library.components.part.instructions-block')
                            <p>
                                Under the hood the accordion uses the <code>{{ '<details>' }}</code>
                                and <code>{{ '<summary>' }}</code> HTML elements for improved semantics
                                and progressive enhancement.
                            </p>
                            <p>
                                Opening/closing animations are non-stacking, meaning that if
                                you click on the accordion item while it is still animating,
                                the current animation will be cancelled before starting the next one.
                            </p>
                            <p>
                                Some basic styles have been applied, but feel free to customize it.
                            </p>
                        @endcomponent
                    </x-slot>
                </x-accordion>
            </div>

</section>
