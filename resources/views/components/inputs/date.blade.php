@props([
    'class' => '',
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'required' => null,
    'disabled' => false,
    'hint' => '',
    'prefix' => null,
    'suffix' => null,
    'id' => $id ?? $name,
    'errors' => null,
])

@php
    $errorClass = $errors && $errors->has($name) ? ' has-error' : '';
    $disabledClass = $disabled ? ' disabled' : '';
    $requiredClass = $required ? ' required' : '';
@endphp

<label class="form-field date {{ $class }} {{ $errorClass }}{{ $disabledClass }}{{ $requiredClass }}" data-form-field>

    @if ($label || $required)
        <div class="input-head">
            @if ($label)
                <div class="label">
                    <span>{{ $label }}</span>
                </div>
            @endif
            @if ($required === 'false')
                <div class="info-text">
                    @lang('common.not_required')
                </div>
            @endunless
    </div>
@endif

<div class="control">

    <input
        id="{{ $id }}"
        data-flatpickr
        name="{{ $name }}"
        type="date"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        {{ $attributes }}
    >

    {!! $slot !!}

    <div class="control-suffix">
        <x-svg size="20" icon="calendar-clock" />
    </div>
</div>
@if ($errors && $errors->has($name))
    <div class="error-container">{{ $errors->first($name) }}</div>
@endif
@if ($hint)
    <div class="hint-container">{{ $hint }}</div>
@endif
</label>
