<h2 class="ui-h3">Display of current SVG sprite-sheet contents</h3>
    <div class="instructions">
        <div class="icon-grid">
            @foreach ($icons as $icon)
                <div>
                    <x-svg icon="{{ $icon }}" />
                    <code>{{ $icon }}</code>
                </div>
            @endforeach
        </div>
    </div>

    <h2 class="ui-h3">Usage</h3>
        <div class="instructions">
            <ol>
                <li>To add new icons, place SVG files in the <code>assets/svg/sprite-icons/**/*.svg</code> directory or it's subdirecotries.</li>

                <li>The sprite-sheet is automatically recompiled via <code>npm run build</code> or <code>npm run dev</code>.</li>

                <li>Each icon is assigned a unique ID based on it's original filename and can be referenced via <code>[icon]</code> attribute in the blade component. Replace <code>icon-name</code> with the actual icon's ID.</li>

                <li>SVG sprite-sheet compiler by default replaces all color values of SVG files to <code>currentColor</code>. This allows us to change SVG colors via CSS <code>color</code> property.
                    <br>

                    <x-code-example language="scss">svg.classname { color: magenta; }</x-code-example>
                </li>

                <li>To preserve original SVG icon colors, it must be whitelisted in <code>vite.config.ts</code> file via<code>dontStripColor</code> option. It supports wildcard selectors. <br>

                    <x-code-example language="ts">dontStripColor: ['logo.svg', 'logo-*.svg', 'flags/*.svg'],</x-code-example>
                </li>
            </ol>

            <p class="note"><strong>Note:</strong> If new icons do not appear after rebuilding the icon sprite, clear your browser cache and restart the browser to load the updated SVG sprite-sheet.</p>
        </div>

        <h2 class="ui-h3">Blade component</h3>
            <div class="instructions">
                <p>Use the <code>x-svg</code> Blade component to render icons from the SVG sprite-sheet.</p>

                <x-code-example language="xml">
                    @verbatim
                        <pre>
<x-svg class="classname" icon="icon-name" size="24" aria-label="Label for the icon"/>
</pre>
                    @endverbatim
                </x-code-example>

                <ul>
                    <li><code>icon</code> (required): The ID of the icon to display, corresponding to the original icon filename without extension. Current sprite icons and their IDs are <a href="#icons">listed here.</a></li>
                    <li><code>class</code> (optional): Additional CSS classes to apply to the SVG element for styling.</li>
                    <li><code>size</code> (optional): Sets both width and height of the SVG. Overrides individual width/height if both are provided.</li>
                    <li><code>width</code> (optional): Sets the width of the SVG. Use with or without height.</li>
                    <li><code>height</code> (optional): Sets the height of the SVG. Use with or without width.</li>
                    <li><code>aria-label</code> (optional but recommended for accessibility): Provides an accessible label for the icon. Should describe the icon's purpose or meaning.</li>
                </ul>

                <p class="note"><strong>Note:</strong> If neither size nor width/height are specified, the SVG will render at its default dimensions defined in the original SVG file.</p>
            </div>
