@props([
    'class' => '',
    'name',
    'label' => null,
    'required' => null,
    'requiredAsterisk' => false,
    'disabled' => false,
    'hint' => '',
    'id' => $name,
    'errors' => null,
])

<label data-form-field {{ $attributes->class(['form-field textarea', 'has-error' => $errors->has($name), 'disabled' => !empty($disabled), 'required' => $required || $requiredAsterisk]) }}>
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
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            @disabled(!empty($disabled))
            @if ($required) required @endif
        ></textarea>
    </div>
    <div class="error-container">
        @error($name)
            {{ $message }}
        @enderror
    </div>
    <div class="hint-container">{{ $hint }}</div>
</label>
