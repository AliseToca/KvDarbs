<div class="demo-modal">
    @foreach ($testimonials as $index => $testimonial)
        @if ($index < 1)
            @include('ui-library.components.part.demo-card', [
                'testimonial' => $testimonial,
            ])
        @endif
    @endforeach
</div>
