<div>
    @foreach ($svgs as $name => $svg)
        <div>
            <div style="width: 150px;">{!! $svg !!}</div>

            <x-code-example language="html">
                {!! $svg !!}
            </x-code-example>
        </div>
    @endforeach
</div>
