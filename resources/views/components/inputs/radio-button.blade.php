<div class="form-field input radio-button {{ !empty($checked) ? 'selected' : '' }}">
    <div class="wrapper">
        <label>
            <span>
                <input
                    type="radio"
                    name="{!! $name !!}"
                    value="{{ $value }}"
                    {{ !empty($checked) ? 'checked' : '' }}
                />
                {{ $label }}
            </span>
        </label>
    </div>
</div>
