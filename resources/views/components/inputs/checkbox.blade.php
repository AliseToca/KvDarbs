@props(['name', 'label', 'value' => 1, 'id' => $id ?? $name, 'checked' => false, 'disabled' => false, 'required' => false, 'requiredAsterisk' => false, 'hint' => '', 'errors' => null, 'errorId' => input_error_id($id ?? $name)])
<div data-form-field {{ $attributes->class(['form-field checkbox', 'has-error' => $errors->has($name), 'disabled' => !empty($disabled), 'required' => !empty($required) || $requiredAsterisk]) }}>
    <label for="{{ $id ?? $name }}">
        <div class="mark">
            <input
                id="{{ $id ?? $name }}"
                type="checkbox"
                name="{{ $name }}"
                value="{{ $value }}"
                @checked(!empty($checked) || old($name) == $value)
                @disabled(!empty($disabled))
                @required(!empty($required))
                @error($name)aria-invalid="true" aria-describedby="{{ $errorId }}"@enderror
            >
            <x-svg
                class="checkmark"
                icon="checkmark"
                size="20"
            />
        </div>
        <span class="label">{!! $label !!}</span>
    </label>
    @if ($errors && $errors->has($name))
        <div class="error-container" id="{{ $errorId }}">
            @error($name)
                {{ $message }}
            @enderror
        </div>
    @endif
    @if ($hint)
        <div class="hint-container">{{ $hint }}</div>
    @endif
</div>
