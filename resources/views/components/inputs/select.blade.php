@props([
    'class' => '',
    'id' => $id ?? $name,
    'name' => '',
    'label' => null,
    'required' => null,
    'disabled' => false,
    'options' => [],
    'hint' => $hint ?? '',
    'errors' => null,
    'errorId' => input_error_id($name),
    'defaultOption' => null,
    'leadingIcon' => null,
])

@php
    $errorClass = $errors && $errors->has($name) ? ' has-error' : '';
    $disabledClass = $disabled ? ' disabled' : '';
    $requiredClass = $required ? ' required' : '';
@endphp

<label
    class="form-field select {{ $class }} {{ $errorClass }}{{ $disabledClass }}{{ $requiredClass }}"
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

        @isset($leadingIcon)
            <div class="leading-suffix">
                <x-svg icon="{{ $leadingIcon }}" size="20" />
            </div>
        @endisset

        <select
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes }}
            @error($name)aria-invalid="true" aria-describedby="{{ $errorId }}"@enderror
        >

            @isset($defaultOption)
                <option
                    value=""
                    disabled
                    selected
                >{{ $defaultOption }}</option>
            @endisset

            @if ($options)
                @foreach ($options as $value => $optionLabel)
                    <option value="{{ $value }}" {{ old($name) == $value && !$defaultOption ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
        <div class="control-suffix">
            <x-svg icon="chevron-down" size="20" />
        </div>
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
