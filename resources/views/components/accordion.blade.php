<details data-accordion {{ $attributes->class(['accordion-item']) }}>
    <summary class="accordion-head">
        {{ $head }}
    </summary>
    <div class="accordion-body" data-accordion-content>
        {!! $body !!}
    </div>
</details>
