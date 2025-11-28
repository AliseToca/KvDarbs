<h2 class="ui-h3">
    CSS Media Query Breakpoints</h3>
    <div class="instructions">

        <p>The project's responsive breakpoints are defined as SCSS variables in <code>@environment/variables/breakpoint.scss</code>. Use these variables in your media queries for consistent responsive design. Example breakpoints include <code>$phone-sm</code>, <code>$phone</code>,<code>$phone-lg</code>, <code>$tablet</code>, and <code>$desktop</code>.</p>

        <p>To write media queries, use the provided mixins from <code>environment/mixins/media.scss</code> for clean and maintainable CSS:</p>

        <ul>
            <li><code>for-at-least($breakpoint)</code>: Targets screens <strong>at least</strong> as wide as the breakpoint.</li>
            <li><code>until($breakpoint)</code>: Targets screens <strong>up to</strong> the breakpoint (exclusive).</li>
            <li><code>between($min-breakpoint, $max-breakpoint)</code>: Targets screens <strong>between</strong> two breakpoints.</li>
        </ul>

        <p>These mixins help keep your CSS DRY and readable, and ensure consistent breakpoints across the project.</p>

        <h2 class="ui-h4">Usage</h4>

            <x-code-example language="scss">
                @verbatim
                    @include for-at-least($desktop) { ... }
                    @include until($tablet) { ... }
                    @include between($phone, $tablet) { ... }
                @endverbatim
            </x-code-example>
    </div>

    <h2 class="ui-h3">Breakpoints List</h3>

        <div class="instructions">
            <p>Add or modify breakpoints in <code>@environment/variables/breakpoint.scss</code> as needed. Use these variables in your media queries for consistent responsive design.</p>

            <x-code-example language="scss">
                @foreach ($content as $item)
                    {{ $item }}
                @endforeach
            </x-code-example>

        </div>
