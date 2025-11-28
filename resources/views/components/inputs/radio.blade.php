@props([
    'label' => '',
    'name',
    'value' => null,
    'checked' => false,
    'placeholder' => '',
    'required' => null,
    'disabled' => false,
    'hint' => '',
    'errors' => null,
    'errorId' => input_error_id($name, $value),
])

<div data-form-field {{ $attributes->class(['form-field radio', 'has-error' => $errors->has($name), 'disabled' => !empty($disabled), 'required' => !empty($required)]) }}>
    <label for="{{ $id ?? $name . '-' . $value }}">
        <div class="mark">
            <input
                id="{{ $id ?? $name . '-' . $value }}"
                type="radio"
                name="{{ $name }}"
                value="{{ $value }}"
                @checked(!empty($checked) || old($name) == $value)
                @disabled(!empty($disabled))
                @required(!empty($required))
                @error($name)aria-invalid="true" aria-describedby="{{ $errorId }}"@enderror
            >
            <span class="checkmark"></span>
        </div>
        <span class="label">{!! $label !!}</span>
    </label>
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
</div>
