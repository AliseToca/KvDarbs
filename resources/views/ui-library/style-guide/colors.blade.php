<h2 class="ui-h3">Color Definition:</h3>
    <div class="instructions">
        <p>Project colors should be defined as CSS variables in <code>@environment/variables/color.scss</code> under the <code>:root</code> selector. Use these variables (e.g., <code>var(--color-primary)</code>) throughout your SCSS for consistency and easy theming. To add or update a color, edit <code>color.scss</code> and reference the new variable in your styles.</p>
    </div>

    <h2 class="ui-h3">All colors</h3>
        <div class="instructions">
            <div class="colors grid-container">
                @foreach ($colors as $color)
                    <div class="color-item">
                        <span class="color-box" style="background: {{ $color['hex'] }}"></span>
                        <code>{{ $color['name'] }}: {{ $color['hex'] }}</code>
                    </div>
                @endforeach
            </div>
        </div>
