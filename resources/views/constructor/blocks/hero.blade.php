<div class="hero-block">
    <img
        class="hero-block-image"
        src="{{ $mediaData['src'] }}"
        srcset="{{ $mediaData['srcset'] }}"
        alt="{{ $block->data->image_alt ?? '' }}"
    />
    @if ($title)
        <div class="hero-block-overlay">
            <h2 class="hero-block-title">{{ $title }}</h2>
        </div>
    @endif
</div>
