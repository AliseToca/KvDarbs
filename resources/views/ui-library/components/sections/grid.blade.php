<section class="ui-section" id="grid">
    <h2 class="ui-h2">Grid system</h2>

    <p>The grid system enables you to build complex layouts using rows and columns with responsive flexibility.</p>

    <br>
    <h2 class="ui-h3">Basic HTML structure</h3>
        <div class="instructions">

            <x-code-example language="html">
                @verbatim
                    <div class="grid-container">
                        <div>Item</div>
                        <div>Item</div>
                        <div>Item</div>
                    </div>
                @endverbatim
            </x-code-example>

        </div>
        <h2 class="ui-h3">Grid Column SCSS mixin</h3>
            <div class="instructions">

                <x-code-example language="scss">
                    @verbatim
                        @include grid-column($column, $offset);
                        @include grid-column($column);
                    @endverbatim
                </x-code-example>

                <p><code>$column: int (required)</code> — Represents the number of columns that the grid item should occupy. By default grid item will occupy all 12 grid columns unless specified otherwise.</p>

                <p><code>$offset: int (optional)</code> — Defines the starting column offset. Set to 0 to start on a new row, or leave blank to flow into the next available column.</p>
            </div>

            <h2 class="ui-h3">Examples</h3>
                <div class="instructions">
                    <div class="grid-container demo">

                        @for ($i = 0; $i < 12; $i++)
                            <div class="grid-column-1"><code>{{ $i + 1 }}</code></div>
                        @endfor

                        <div>
                            <code>By default, each grid item spans all 12 columns unless a specific width is defined.</code>
                        </div>

                        <div class="item-1">
                            <code>grid-column(4)</code>
                        </div>
                        <div class="item-2">
                            <code>grid-column(4)</code>
                        </div>
                        <div class="item-3">
                            <code>grid-column(4)</code>
                        </div>
                        <div class="item-4">
                            <code>grid-column(3, 2)</code>
                        </div>

                        <div class="item-5">
                            <code>grid-column(3)</code>
                        </div>
                        <div class="item-8">
                            <code>grid-column(3, 9)</code>
                        </div>

                        <div class="item-7">
                            <code>grid-column(3)</code>
                        </div>

                        <div class="item-6">
                            <code>grid-column(3, 0)</code>
                        </div>
                    </div>
                </div>
</section>
