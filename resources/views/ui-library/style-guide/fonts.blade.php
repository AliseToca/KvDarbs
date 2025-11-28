<h2 class="ui-h3">Adding Webfonts</h3>
    <div class="instructions">
        <p>To add webfonts to the project, follow these steps:</p>
        <ol>
            <li>Place your font files (e.g., .woff2, .woff, .ttf) in <code>resources/assets/fonts/</code>.</li>
            <li>In your SCSS, import the font files using <code>@font-face</code> declarations. Example:

                <x-code-example language="scss">
                    <pre>
                @font-face {
                    font-family: "MyFont";
                    src: url("../fonts/MyFont.woff2") format("woff2"),
                         url("../fonts/MyFont.woff") format("woff");
                    font-weight: 400;
                    font-style: normal;
                    font-display: swap;
                }
                </pre>
                </x-code-example>
            </li>
            <li>Reference the font in your stylesheets using <code>font-family: 'MyFont', sans-serif</code>.<br><strong>Note:</strong> Always include appropriate fallback font families for better compatibility and accessibility.
            </li>
            <li>Vite will bundle and copy font files referenced in your SCSS to the <code>public/build/fonts/</code> directory during build.</li>
            <li>If you add new fonts, restart the Vite dev server to ensure changes are picked up.</li>
        </ol>

        <p class="note">
            <strong>Note:</strong> <span>When using Google Fonts or other free to use fonts, always use a font conversion tool such as <a href="https://transfonter.org/" target="_blank">Transfonter</a> to convert fonts to webfont formats and self-host them. This reduces DNS lookup times and improves privacy.</span>
        </p>
        <p class="note">
            <strong>Licensing:</strong> <span>Always check in with the project manager (PM) about purchasing appropriate fonts and font licenses before using commercial fonts.</span>
        </p>
    </div>

    <h2 class="ui-h3">Project webfont list</h3>
        <div class="instructions">
            <p>This list will automatically show installed webfonts previews below.</p>
            <div class="fontface-list">
                @foreach ($fonts as $font)
                    @php
                        $style = "style=\"font-family: '{$font['familyName']}'; font-weight: {$font['weight']}; font-style: {$font['style']};\"";
                    @endphp
                    <div class="font-face">
                        <h2 class="ui-h2" {!! $style !!}>{{ $font['familyName'] }} {{ $font['weight'] }} {{ $font['style'] }}</h2>
                        <div {!! $style !!}>
                            <p>The quick brown fox jumps over the lazy dog.</p>
                            <p>Glāžšķūņu rūķīši.</p>
                            <p>Чушь: гид вёз кэб цапф, юный жмот съел хрящ.</p>
                            <p>0123456789 !@#$%^&*()_+-=[]{}|;':",.<>/?`~</p>
                        </div>
                    </div>
                @endforeach
                </ul>
            </div>
