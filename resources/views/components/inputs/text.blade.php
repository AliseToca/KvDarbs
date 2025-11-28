@props([
    'class' => '',
    'name',
    'label',
    'value' => null,
    'id' => $id ?? $name,
    'placeholder' => '',
    'required' => null,
    'requiredAsterisk' => false,
    'disabled' => false,
    'hint' => '',
    'prefix' => null,
    'suffix' => null,
    'type' => $type ?? 'text',
    'errors' => null,
    'errorId' => input_error_id($id ?? $name),
])

@php
    $errorClass = $errors && $errors->has($name) ? ' has-error' : '';
    $disabledClass = $disabled ? ' disabled' : '';
    $requiredClass = $required || $requiredAsterisk ? ' required' : '';
@endphp

<label
    class="form-field text {{ $class }} {{ $errorClass }}{{ $disabledClass }}{{ $requiredClass }}"
    data-form-field
    for="{{ $id }}"
>
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
            @endif
        </div>
    @endif

    <div class="control">
        @if ($prefix)
            <div class="control-prefix">{!! $prefix !!}</div>
        @endif
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            {{ $attributes }}
            @error($name)aria-invalid="true" aria-describedby="{{ $errorId }}"@enderror
        >

        {!! $slot !!}

        @if ($errors && $errors->has($name))
            <div class="control-suffix">
                <x-svg icon="alert-circle" size="24" />
            </div>
        @endif
        @if ($suffix)
            <div class="control-suffix">{!! $suffix !!}</div>
        @endif
    </div>
    @isset($errors)
        @error($name)
            <div class="error-container" id="{{ $errorId }}">
                @error($name)
                    {{ $message }}
                @enderror
            </div>
        @enderror
    @endisset
    @isset($hint)
        <div class="hint-container">{{ $hint }}</div>
    @endisset
</label>
