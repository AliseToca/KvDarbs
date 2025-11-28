@props(['tabs', 'panelClass' => ''])

<div
    class="tabs"
    data-tabbed-content
    role="tablist"
>
    <div class="tab-container">
        <div class="tab-scroller">
            <div class="tab-list">
                @foreach ($tabs as $index => $tab)
                    <x-button
                        class="blank"
                        id="tab{{ $index }}"
                        data-tab
                        role="tab"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="panel{{ $index }}"
                        tabindex="{{ $index === 0 ? '0' : '-1' }}"
                        size="sm"
                        text="{{ $tab }}"
                    />
                @endforeach
            </div>
        </div>
    </div>

    <div class="{{ $panelClass }} panel-container">
        @foreach ($tabs as $index => $tab)
            <div
                id="panel{{ $index }}"
                data-tab-panel
                role="tabpanel"
                aria-labelledby="tab{{ $index }}"
                aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
            >
                @isset(${'panel' . $index})
                    {{ ${'panel' . $index} }}
                @endisset
            </div>
        @endforeach
    </div>
</div>
