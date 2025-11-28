@props([
    'class' => '',
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'required' => null,
    'disabled' => false,
    'hint' => '',
    'id' => $id ?? $name,
    'errors' => null,
    'errorId' => input_error_id($id ?? $name),
    'maxSize' => null,
])

@php
    $errorClass = $errors && $errors->has($name) ? ' has-error' : '';
    $disabledClass = $disabled ? ' disabled' : '';
    $requiredClass = $required ? ' required' : '';
@endphp

<div
    class="form-field file {{ $class }} {{ $errorClass }}{{ $disabledClass }}{{ $requiredClass }}"
    data-form-field
    data-file-upload
    data-i18n-some-not-added="@lang('inputs.file.some_not_added')"
    data-i18n-too-large="@lang('inputs.file.too_large')"
    data-i18n-limit-is="@lang('inputs.file.limit_is')"
    data-i18n-type-not-allowed="@lang('inputs.file.type_not_allowed')"
>
    @if ($label || $required)
        <div class="input-head">
            @if ($label)
                <div class="label">
                    <label for="{{ $id }}">{{ $label }}</label>
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

        <div class="upload-container">
            <div class="upload-info">
                <div class="upload-icon">
                    <x-svg icon="arrow-down" size="24" />
                </div>

                <div class="info">
                    <label for="{{ $id }}">@lang('inputs.file.add_file_here')</label>
                    <p>@lang('inputs.file.click_to_browse')
                        @if ($maxSize)
                            (max: {{ $maxSize }} MB)
                        @endif
                    </p>
                </div>
            </div>
            <div class="file-list" data-attached-files></div>
            <template id="fileItemTemplate">
                <div class="file-item">
                    <x-button
                        class="third tag"
                        size="xs"
                        icon="close"
                        text=" "
                    ><span class="file-item-name" data-file-name></span></x-button>
                </div>
            </template>
        </div>

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="file"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            {{ $attributes }}
            @if ($maxSize) data-max-size-mb="{{ $maxSize }}" @endif
            @error($name)aria-invalid="true" aria-describedby="{{ $errorId }}"@enderror
        >

        {!! $slot !!}

        @if ($errors && $errors->has($name))
            <div class="control-suffix">
                <x-svg icon="alert-circle" size="24" />
            </div>
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
</div>
