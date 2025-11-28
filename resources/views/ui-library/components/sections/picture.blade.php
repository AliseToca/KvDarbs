<section class="ui-section" id="picture">
    <h2 class="ui-h2">Picture</h2>
    <p>The Picture component creates responsive images with automatic WebP variants and progressive loading. Provides unified processing for both Curator Media objects and static assets from <code>resources/assets/images/</code>.</p>
    <ul>
        <li>Modern format support (WebP) with progressive loading</li>
        <li>Context-aware image loading (hero, card, gallery, content, thumbnail)</li>
        <li>Lazy loading enabled by default (disable with <code>:lazy="false"</code>)</li>
        <li>Support for both Curator Media objects and static Vite assets</li>
        <li>Automatic srcset generation based on predefined glide preset</li>
        <li>Integration with Laravel Glide for on-demand processing</li>
        <li>Intrinsic aspect ratio via width/height attributes to avoid layout shifts</li>
    </ul>

    <h3 class="ui-h3">Demo</h3>

    @component('ui-library.components.part.instructions-block')
        <div class="grid-container picture-demo-showcase">
            <div class="column-8">
                <x-picture
                    :media-data="$demoImage"
                    alt="Example: Static asset with automatic responsive WebP variant generation and progressive loading support"
                    caption="This is additional context for the image"
                    :lazy="true"
                />
            </div>
        </div>
    @endcomponent

    <h3 class="ui-h3">Blade Component</h3>
    @component('ui-library.components.part.instructions-block')

        <x-code-example language="xml">
            @verbatim
                <x-picture
                    class="my-image-class"
                    alt="Responsive image description"
                    caption="This is additional context for the image"
                    :src="$media"
                    sizes="(max-width: 640px) 100vw, 50vw"
                    :lazy="true"
                    context="card"
                />
            @endverbatim
        </x-code-example>
        <p>Use the <code>x-picture</code> component to create responsive images with automated srcset generation. Supports Curator Media objects and static assets with bandwidth-friendly responsive variants.</p>

        <h4 class="ui-h4">Context presets</h4>
        <p>The optional <code>context</code> attribute maps to predefined <code>sizes</code> strings inside <code>resources/views/components/picture.blade.php</code>.</p>

        <table>
            <tr>
                <th>Context Value</th>
                <th>Sizes Attribute Value</th>
            </tr>
            <tr>
                <td><code>hero</code></td>
                <td><code>100vw</code></td>
            </tr>
            <tr>
                <td><code>card</code></td>
                <td><code>(max-width: 30rem) 50vw, (max-width: 48rem) 33vw, 25vw</code></td>
            </tr>
            <tr>
                <td><code>thumbnail</code></td>
                <td><code>(max-width: 30rem) 25vw, (max-width: 48rem) 20vw, 15vw</code></td>
            </tr>
            <tr>
                <td><code>gallery</code></td>
                <td><code>(max-width: 30rem) 100vw, (max-width: 48rem) 50vw, 33vw</code></td>
            </tr>
            <tr>
                <td><code>content</code></td>
                <td><code>(max-width: 48rem) 100vw, 50vw</code></td>
            </tr>
            <tr>
                <td><code>default</code></td>
                <td><code>(max-width: 30rem) 100vw, (max-width: 48rem) 50vw, 33vw</code></td>
            </tr>
        </table>
        <p>Adjust or extend the presets by editing the <code>$contextSizes</code> array in the component. If you omit <code>context</code>, the <code>default</code> preset is used; passing an explicit <code>sizes</code> attribute always wins.</p>
        <p>When you call <code>ImageService::getImageAndSourceSetUrls()</code>, forward the returned <code>width</code> and <code>height</code> values to the <code>x-picture</code> component. The intrinsic dimensions are written to the rendered <code>&lt;img&gt;</code> element so the browser can reserve space up front and avoid layout shifts.</p>
    @endcomponent

    <h3 class="ui-h3">Sizes Attribute</h3>
    @component('ui-library.components.part.instructions-block')
        <p>The <code>sizes</code> attribute tells the browser how much horizontal space (CSS width) the image will occupy under different viewport conditions. The browser uses this together with the <code>srcset</code> width descriptors (the <code>320w</code>, <code>640w</code>, etc.) to choose the most appropriate file to download <em>before</em> layout and without wasting bandwidth.</p>
        <ul>
            <li><strong>Syntax:</strong> A comma‑separated list of media condition + slot size pairs, ending with a default size: <code>(media) slot, (media) slot, defaultSlot</code>.</li>
            <li><strong>Example:</strong> <code>(max-width: 30rem) 100vw, (max-width: 48rem) 50vw, 33vw</code> means:
                <ul>
                    <li>If viewport ≤ 30rem: image renders full width (<code>100vw</code>).</li>
                    <li>Else if viewport ≤ 48rem: it takes half the viewport (<code>50vw</code>).</li>
                    <li>Else: ~one third of the viewport (<code>33vw</code>).</li>
                </ul>
            </li>
            <li><strong>No media condition on last value:</strong> The final size acts as the fallback for all larger (or unmatched) viewports.</li>
            <li><strong>Units:</strong> Use <code>vw</code> (viewport width) for fluid layouts; fixed <code>px</code> values are okay when the rendered width is constant (e.g. logos).</li>
            <li><strong>Accuracy matters:</strong> Over‑estimating the slot size causes the browser to download a larger image than necessary; under‑estimating can lead to upscaling and softness.</li>
        </ul>

        <h4 class="ui-h4">Common Patterns</h4>
        <table>
            <tr>
                <th>Layout Scenario</th>
                <th>Suggested <code>sizes</code> value</th>
                <th>Notes</th>
            </tr>
            <tr>
                <td>Full-width hero</td>
                <td><code>100vw</code></td>
                <td>Always spans viewport width</td>
            </tr>
            <tr>
                <td>Two-column content (image half width ≥ md)</td>
                <td><code>(max-width: 48rem) 100vw, 50vw</code></td>
                <td>Stacks on small screens, halves on larger</td>
            </tr>
            <tr>
                <td>Three-column grid (desktop)</td>
                <td><code>(max-width: 48rem) 50vw, 33vw</code></td>
                <td>Two cols tablet, three cols desktop</td>
            </tr>
            <tr>
                <td>Fixed card thumbnail (e.g. 300px)</td>
                <td><code>300px</code></td>
                <td>Predictable slot width</td>
            </tr>
        </table>

        <h4 class="ui-h4">When to Provide <code>sizes</code></h4>
        <ul>
            <li><strong>Use <code>context</code> only:</strong> For common layouts (hero, card, gallery, content) letting the component derive sensible defaults.</li>
            <li><strong>Provide custom <code>sizes</code>:</strong> When the image width differs from provided contexts (e.g. nested grids, unusual aspect-ratio slots).</li>
            <li><strong>Fixed-width elements:</strong> Use an explicit pixel width, e.g. <code>sizes="200px"</code>.</li>
        </ul>

        <h4 class="ui-h4">Context-Based Sizes</h4>
        <table>
            <tr>
                <th>Context</th>
                <th>Sizes Attribute</th>
                <th>Description</th>
            </tr>
            <tr>
                <td><code>hero</code></td>
                <td><code>100vw</code></td>
                <td>Full viewport width hero images</td>
            </tr>
            <tr>
                <td><code>card</code></td>
                <td><code>(max-width: 30rem) 50vw, (max-width: 48rem) 33vw, 25vw</code></td>
                <td>Card thumbnails in grids</td>
            </tr>
            <tr>
                <td><code>thumbnail</code></td>
                <td><code>(max-width: 30rem) 25vw, (max-width: 48rem) 20vw, 15vw</code></td>
                <td>Small thumbnails</td>
            </tr>
            <tr>
                <td><code>gallery</code></td>
                <td><code>(max-width: 30rem) 100vw, (max-width: 48rem) 50vw, 33vw</code></td>
                <td>Gallery grid images</td>
            </tr>
            <tr>
                <td><code>content</code></td>
                <td><code>(max-width: 48rem) 100vw, 50vw</code></td>
                <td>Images within text content</td>
            </tr>
        </table>

        <h4 class="ui-h4">Troubleshooting</h4>
        <ul>
            <li><strong>Image downloaded is too large:</strong> Reduce the declared slot size in <code>sizes</code>.</li>
            <li><strong>Blurry image:</strong> Increase slot size or ensure the original file is large enough so the browser can pick an appropriately sized variant.</li>
            <li><strong>Unexpected variant widths:</strong> The service prunes variants above the original intrinsic width (no upscaling).</li>
        </ul>

        <p class="note"><strong>Tip:</strong> Use browser dev tools (Network tab → Img) to verify which variant is selected at different viewport widths.</p>
    @endcomponent

    <h3 class="ui-h3">Usage Examples</h3>
    @component('ui-library.components.part.instructions-block')
        <h4 class="ui-h4">Basic Usage with Curator Media</h4>
        <p>Best option for managed media - automatically generates WebP variants with progressive loading and JPG fallback
            for maximum compatibility.</p>

        <x-code-example language="php">
            @verbatim
                // In your controller
                $heroImage = Media::find(1);
                resolve(ImageService::class)>getImageAndSourceSetUrls($heroImage),

                // For static assets or images uploaded via the default FileUpload field
                $image = '/path/to/image.jpg';
                resolve(ImageService::class)->getImageAndSourceSetUrls($image),

                // In your view
                <x-picture
                    alt="Hero image"
                    :src="$heroImageSrc"
                    :srcset="$heroImageSrcset"
                />
            @endverbatim
        </x-code-example>

        <h4 class="ui-h4">Above-the-fold Images</h4>
        <p>Explicitly disable lazy loading for critical images that appear immediately when the page loads.</p>

        <x-code-example language="php">
            @verbatim
                <x-picture
                    class="hero-image"
                    alt="Hero image"
                    context="hero"
                    :src="$heroImageSrc"
                    :srcset="$heroImageSrcset"
                    :lazy="false"
                />
            @endverbatim
        </x-code-example>

        <h4 class="ui-h4">Context-Aware Sizing</h4>
        <p>Use predefined contexts for optimal image sizing based on how the image will be displayed.</p>

        <x-code-example language="php">
            @verbatim
                // Hero image (full viewport width)
                <x-picture
                    alt="Hero"
                    context="hero"
                    :src="$hero"
                />

                // Card thumbnail (smaller, responsive)
                <x-picture
                    alt="Card"
                    :src="$card"
                    context="card"
                />

                // Gallery image (responsive grid)
                <x-picture
                    alt="Gallery"
                    :src="$gallery"
                    context="gallery"
                />

                // Content image (within text)
                <x-picture
                    alt="Content"
                    :src="$content"
                    context="content"
                />
            @endverbatim
        </x-code-example>

        <h4 class="ui-h4">Custom Responsive Sizing</h4>
        <p>Override automatic sizing with custom sizes attribute when needed.</p>

        <x-code-example language="php">
            @verbatim
                // Custom sizes for specific layouts
                <x-picture
                    alt="Custom"
                    :src="$image"
                    sizes="(max-width: 30rem) 100vw, 50vw"
                />
            @endverbatim
        </x-code-example>

        <h4 class="ui-h4">With Caption</h4>
        <p>Add a caption below the image using the alt text. Includes proper semantic markup and ARIA attributes.</p>

        <x-code-example language="php">
            @verbatim
                // Image with caption (uses alt text)
                <x-picture
                    alt="Beautiful mountain landscape at sunset"
                    :src="$galleryImage"
                    context="gallery"
                    caption="This is additional context for the image"
                />
            @endverbatim
        </x-code-example>
    @endcomponent

    <h3 class="ui-h3">Laravel Glide Configuration</h3>
    @component('ui-library.components.part.instructions-block')
        <p>The Picture component integrates with Laravel Glide for image processing. Configuration is managed in
            <code>config/laravel-glide.php</code>:
        </p>

        <h4 class="ui-h4">Processing Settings</h4>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>Driver</td>
                <td><code>gd</code></td>
                <td>Uses PHP GD library for image processing</td>
            </tr>
            <tr>
                <td>Output Disk</td>
                <td><code>public</code></td>
                <td>Processed images stored in storage/app/public/</td>
            </tr>
            <tr>
                <td>Formats</td>
                <td>JPEG, PNG, WebP</td>
                <td>Supported input/output image formats</td>
            </tr>
        </table>

        <h4 class="ui-h4">Image Quality</h4>
        <ul>
            <li><strong>WebP:</strong> 85% quality (optimal size/quality balance)</li>
            <li><strong>Fit Strategy:</strong> <code>contain</code> (maintains aspect ratio)</li>
        </ul>

        <h4 class="ui-h4">Integration Notes</h4>
        <ul>
            <li>Curator Media objects use built-in Glide processing via <code>getSignedUrl()</code></li>
            <li>Dynamic image generation - no need to use presets directly</li>
            <li>Automatic WebP generation for browser compatibility</li>
            <li>On-demand processing with intelligent caching</li>
        </ul>
    @endcomponent

</section>
