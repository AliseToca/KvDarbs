@php

    $buttonStates = [
        'default' => [
            'class' => '',
            'label' => 'Default button',
            'description' => 'The default button with preset states and transitions. Acts as a template for further customization.',
        ],
        'icon-leading' => [
            'class' => 'icon',
            'label' => 'Icon leading',
            'icon-start' => 'chevron-down',
            'description' => 'Leading icon to button.',
        ],
        'icon' => [
            'class' => 'icon',
            'icon' => 'chevron-down',
            'label' => 'Icon trailing',
            'description' => 'Trailing icon to button.',
        ],
        'disabled' => [
            'class' => 'disabled',
            'label' => 'Disabled button',
            'description' => 'Attribute (not classname) for disabled buttons',
            'disabled' => 'disabled',
        ],
        'icon-only' => [
            'class' => 'icon-only',
            'icon' => 'chevron-down',
            'label' => '',
            'description' => 'For buttons that contain only an SVG icon. Has equal W&H dimensions.',
        ],
    ];

    $buttonSizes = [
        'xs' => [
            'class' => 'medium',
        ],
        'sm' => [
            'class' => 'medium',
        ],
        'md' => [
            'class' => 'medium',
        ],
        'lg' => [
            'class' => 'large',
        ],
    ];

    $buttonVariations = [
        'cta' => [
            'class' => 'cta',
            'label' => 'Call to action',
            'description' => 'CTA button',
        ],
        'primary' => [
            'class' => 'primary',
            'label' => 'Primary',
            'description' => 'For filled type buttons. Example usage: <code>class="button primary"</code>',
        ],
        'secondary' => [
            'class' => 'secondary',
            'label' => 'Secondary',
            'description' => 'For "ghost" type buttons.',
        ],
        'link' => [
            'class' => 'link',
            'label' => 'Link button',
            'description' => 'Link buttons without borders',
        ],
        'blank' => [
            'class' => 'blank',
            'label' => 'Blank button',
            'description' => 'For buttons with no styling. Use for custom buttons only.',
        ],
    ];
@endphp
@php
    // Prepare combinations grouped by size for clearer layout.
    $comboCollection = collect($buttonStyles['combinations'] ?? []);
    $combinationsBySize = $comboCollection->groupBy('size');
@endphp
<section class="ui-section" id="buttons">
    <h2 class="ui-h2">Buttons</h2>

    <h2 class="ui-h3">Blade component</h3>
        <div class="instructions">

            <x-code-example language="xml">
                @verbatim
                    <x-button
                        class="primary"
                        size="lg"
                        icon="arrow-down"
                        icon-size="24"
                        href="https://example.com"
                        text="Button label"
                        aria-label="Button aria label"
                    />
                @endverbatim
            </x-code-example>
            <p>Use the <code>x-button</code> component to create buttons. You can specify class, size, and icon attributes as needed. Icons are rendered using the SVG sprite-sheet by default.</p>
        </div>

        <h2 class="ui-h3">Button showcase</h2>
        <div class="instructions button-showcase">
            @forelse ($combinationsBySize as $size => $combos)
                <div class="button-type-group">

                    <div class="button-size-group">
                        <code>{{ $size }}</code>
                        @foreach ($combos as $combo)
                            @php
                                $type = $combo['type'];
                                $btnClasses = trim($type !== 'default' ? $type : '');
                                $isIconOnly = $type === 'icon-only';
                            @endphp
                            <div class="button-container">
                                @if ($isIconOnly)
                                    <x-button
                                        class="{{ $btnClasses }}"
                                        size="{{ $combo['size'] }}"
                                        icon="chevron-down"
                                        icon-size="24"
                                    />
                                @else
                                    <x-button
                                        class="{{ $btnClasses }}"
                                        size="{{ $combo['size'] }}"
                                        text="{{ $combo['label'] }}"
                                    />
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p>No button combinations available.</p>
            @endforelse
        </div>

        <h2 class="ui-h3">Button variations</h3>
            @component('ui-library.components.part.instructions-block')
                <table class="button-table">
                    <tr>
                        <th>Classname/attribute</th>
                        <th>Example</th>
                        <th width="50%">Description</th>
                    </tr>
                    <tr>
                        <td><code>.button</code></td>
                        <td>
                            <x-button text="Default button" />
                        </td>
                        <td>The default button with preset states and transitions. Acts as a template for further customization.</td>
                    </tr>
                    <tr>
                        <td><code>.button</code></td>
                        <td>
                            <x-button icon-start="arrow-down" text="Default with icon" />
                        </td>
                        <td>Default button with a leading icon. Use <code>icon-start</code> attribute to add an icon before the text. Use <code>icon</code> attribute for a trailing icon.</td>
                    </tr>
                    <tr>
                        <td><code>.link</code></td>
                        <td>
                            <x-button class="link" text="Link button" />
                        </td>
                        <td>Link buttons without borders</td>
                    </tr>
                    <tr>
                        <td><code>.primary</code></td>
                        <td>
                            <x-button class="primary" text="Primary" />
                        </td>
                        <td>For filled type buttons. Example usage: <code>class="button primary"</code></td>
                    </tr>
                    <tr>
                        <td><code>.secondary</code></td>
                        <td>
                            <x-button class="secondary" text="Secondary" />
                        </td>
                        <td>For 'ghost' type buttons.</td>
                    </tr>
                    <tr>
                        <td><code>.third</code></td>
                        <td>
                            <x-button class="third" text="Third style button" />
                        </td>
                        <td>Third style type buttons.</td>
                    </tr>
                    <tr>
                        <td><code>.icon</code></td>
                        <td>
                            <x-button
                                class="primary"
                                icon-start="checkmark"
                                text="Default with icon"
                            />
                        </td>
                        <td>Leading icon to button.</td>
                    </tr>
                    <tr>
                        <td><code>[icon="sprite_id"]</code></td>
                        <td>
                            <x-button icon="chevron-right" text="Trailing icon to button" />
                        </td>
                        <td>Trailing icon + large size button.</td>
                    </tr>
                    <tr>
                        <td><code>[size="sm"]</code></td>
                        <td>
                            <x-button size="sm" text="Small" />
                        </td>
                        <td>For buttons with reduced height.</td>
                    </tr>
                    <tr>
                        <td><code>.icon-only</code></td>
                        <td>
                            <x-button
                                class="primary"
                                icon-size="24"
                                icon="arrow-down"
                            />
                        </td>
                        <td>For buttons that contain only an svg icon. Has equal padding on all sides.</td>
                    </tr>
                    <tr>
                        <td><code>disabled</code></td>
                        <td>
                            <x-button class="button primary" disabled>Disabled</x-button>
                        </td>
                        <td>Attribute (not classname) for disabled buttons</td>
                    </tr>
                </table>
            @endcomponent

            <h2 class="ui-h3">Button size and padding</h3>
                @component('ui-library.components.part.instructions-block')
                    <p>Set the button height with <code>--button-min-height</code> according to design. Setting the minimum height ensures that the button can expand vertically if this is required by the content height or wrapping (which should be avoided if possible).</p>
                    <p>Set the horizontal padding using <code>--button-padding-inline</code>.</p>
                    <p>Vertical padding is computed from the values of <code>--button-min-height</code>,<code>--button-line-height</code> and <code>--button-border-width</code>.</p>
                @endcomponent

                <h2 class="ui-h3">Buttons with icons</h3>
                    @component('ui-library.components.part.instructions-block')
                        <p>Insert icons directly inside the button element. No manual spacing is needed — CSS gap handles alignment and spacing automatically.</p>
                        <x-button
                            class="primary"
                            size="lg"
                            icon-start="chevron-down"
                            icon="chevron-down"
                            text="Download"
                        />
                    @endcomponent
</section>
