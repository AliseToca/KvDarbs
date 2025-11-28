<section class="ui-section" id="form-fields">
    <h2 class="ui-h2">Form fields</h2>

    <h3 class="ui-h3">Text field</h3>
    @component('ui-library.components.part.instructions-block')
        <p>Use the <code>x-inputs.text</code> or <code>x-inputs.textarea</code> component. Refer to the <a href="https://laravel.com/docs/blade">Laravel Blade component documentation</a> for additional details on props, slots, and component inheritance.</p>

        <div class="info-group">
            <table>
                <caption>Properties</caption>
                <tr>
                    <td>Required</td>
                    <td><code>name</code></td>
                </tr>
                <tr>
                    <td>Optional</td>
                    <td><code>id</code>, <code>label</code>, <code>value</code><code>placeholder</code>, <code>hint</code>, <code>disabled</code>,<code>required</code>.</td>
                </tr>
            </table>
        </div>
        <div class="info-group">
            <h2 class="ui-h4">Slots</h4>
                <p>The <span class="underline">text</span> field has predefined <code>prefix</code> and <code>suffix</code> slots for icons, units or other content.</p>
        </div>

        <x-code-example language="blade">
            @verbatim
                <x-inputs.text
                    name="phone_number"
                    label="Phone number"
                    placeholder="Your phone number"
                >
                    <x-slot name="suffix">Units</x-slot>
                </x-inputs.text>
            @endverbatim
        </x-code-example>

        <div class="fields-container grid-container">
            <x-inputs.text
                name="text_field"
                label="Basic text field"
                required=true
            />
            <x-inputs.text
                name="decorated_field"
                label="Fully decorated"
                placeholder="Placeholder"
                hint="This is a hint"
                :required="true"
            >
                <x-slot name="prefix">
                    <x-svg size="20" icon="arrow-down"></x-svg>
                </x-slot>
                <x-slot name="suffix">Units</x-slot>
            </x-inputs.text>
            <x-inputs.text
                id="error_inputddddd"
                name="text_with_error"
                label="With error"
                :errors="$errors"
            />
            <x-inputs.text
                name="text_field"
                label="Disabled"
                disabled
            />
            <x-inputs.textarea name="textarea_field" label="Textarea field" />
        </div>
    @endcomponent

    <h3 class="ui-h3">Datepicker</h3>
    <p>
        For datepicker component use the <code>x-inputs.date</code> component. It utilizes the Flatpickr library.
    </p>
    <p>
        Datepicker mode can be set to <code>single</code>, <code>multiple</code> or <code>range</code> via the
        <code>mode</code> attribute.
    </p>

    <p>
        To display datepicker component inline, add the <code>inline</code> attribute.
    </p>
    @component('ui-library.components.part.instructions-block')

        <x-code-example language="blade">
            @verbatim
                <x-inputs.date
                    name="date-picker-example"
                    placeholder="Pick a date"
                    mode="range"
                    inline
                />
            @endverbatim
        </x-code-example>

        <div class="fields-container grid-container">
            <x-inputs.date name="date-picker-basic" placeholder="Pick a single date" />
            <x-inputs.date
                name="date-picker-range"
                placeholder="Pick a date range"
                mode="range"
            />
            <x-inputs.date
                name="date-picker-inline"
                placeholder="Pick multiple dates"
                mode="multiple"
                inline
            />
        </div>
    @endcomponent

    <h3 class="ui-h3">Select field</h3>
    @component('ui-library.components.part.instructions-block')
        <p>Use the <code>x-inputs.select</code> component.</p>

        <div class="info-group">
            <table>
                <caption>Properties</caption>
                <tr>
                    <td>
                        Required
                    </td>
                    <td>
                        <code>name</code>, <code>options</code>
                    </td>
                </tr>
                <tr>
                    <td>
                        Optional
                    </td>
                    <td>
                        <code>id</code>, <code>label</code>, <code>hint</code>,
                        <code>disabled</code>, <code>required</code>
                    </td>
                </tr>
            </table>
        </div>

        <x-code-example language="blade">
            @verbatim
                <x-inputs.select
                    name="country"
                    label="Country"
                    :options="['', 'Latvia', 'Estonia', 'Lithuania', 'Other']"
                />
            @endverbatim
        </x-code-example>
        <div class="fields-container grid-container">
            <x-inputs.select
                id="select_basic"
                name="select_basic"
                required="true"
                label="Basic field"
                hint="Some useful information"
                :options="[
                    '' => 'Izvēlēties',
                    'lv' => 'Latvia',
                    'ee' => 'Estonia',
                    'lt' => 'Lithuania',
                    'Other' => 'other',
                ]"
            />
            <x-inputs.select
                name="select_with_hint"
                label="With hint"
                :options="['', 'Latvia', 'Estonia', 'Lithuania', 'Other']"
                hint="Some useful information"
            />
            <x-inputs.select
                name="select_with_error"
                label="With error"
                required="true"
                :options="['', 'Latvia', 'Estonia', 'Lithuania', 'Other']"
            />
            <x-inputs.select
                name="select_disabled"
                label="Disabled"
                required="false"
                :options="['', 'Latvia', 'Estonia', 'Lithuania', 'Other']"
                :disabled="true"
            />
        </div>
    @endcomponent

    <h3 class="ui-h3">Checkbox / Radio button</h3>
    @component('ui-library.components.part.instructions-block')
        <p>
            Use the <code>x-inputs.checkbox</code> or <code>x-inputs.radio</code>
            components respectively.
        </p>

        <div class="info-group">
            <table>
                <caption>Checkbox properties</caption>
                <tr>
                    <td>
                        Required
                    </td>
                    <td>
                        <code>name</code>, <code>label</code>
                    </td>
                </tr>
                <tr>
                    <td>
                        Optional
                    </td>
                    <td>
                        <code>id</code>, <code>value</code>, <code>checked</code>,
                        <code>disabled</code>, <code>required</code>
                    </td>
                </tr>
            </table>
        </div>

        <x-code-example language="blade">
            @verbatim
                <x-inputs.checkbox name="newsletter" label="Subscribe to newsletter" />
            @endverbatim
        </x-code-example>

        <div class="info-group">
            <table>
                <caption>Radio properties</caption>
                <tr>
                    <td>
                        Required
                    </td>
                    <td>
                        <code>name</code>, <code>label</code>, <code>value</code>
                    </td>
                </tr>
                <tr>
                    <td>
                        Optional
                    </td>
                    <td>
                        <code>id</code>, <code>checked</code>, <code>disabled</code>,
                        <code>required</code>
                    </td>
                </tr>
            </table>
        </div>

        <x-code-example language="blade">
            @verbatim
                <x-inputs.radio
                    name="radio"
                    value="1"
                    label="One"
                />
                <x-inputs.radio
                    name="radio"
                    value="2"
                    label="Two"
                    :checked="true"
                />
            @endverbatim
        </x-code-example>

        <div class="fields-container grid-container">
            <div>
                <x-inputs.radio
                    name="radio"
                    value="0"
                    label="Test case with very long text label that wraps in multiple lines, so we can test input box positioning."
                />
                <x-inputs.radio
                    name="radio"
                    value="1"
                    label="Basic radio one"
                />
                <x-inputs.radio
                    name="radio"
                    value="2"
                    label="Basic radio two"
                    :checked="true"
                />
                <x-inputs.radio
                    name="radio_with_error"
                    value="3"
                    label="With error"
                />
                <x-inputs.radio
                    name="radio"
                    value="4"
                    label="Disabled"
                    :disabled="true"
                />
            </div>
            <div>
                <x-inputs.checkbox name="test_checkbox" label="Test case with very long text label that wraps in multiple lines, so we can test input box positioning." />

                <x-inputs.checkbox name="checkbox_basic" label="Normal checkbox" />
                <x-inputs.checkbox
                    name="checkbox_required"
                    label="Required"
                    :required="true"
                />
                <x-inputs.checkbox name="checkbox_with_error" label="With error" />
                <x-inputs.checkbox
                    name="checkbox_disabled"
                    label="Disabled"
                    :disabled="true"
                />
            </div>
        </div>
    @endcomponent

    <h3 class="ui-h3">File Upload Input field</h3>

    @component('ui-library.components.part.instructions-block')
        <p>Use the <code>x-inputs.file</code> component.</p>
        <div class="info-group">
            <table>
                <caption>Properties</caption>
                <tr>
                    <td>
                        Required
                    </td>
                    <td>
                        <code>name</code>
                    </td>
                </tr>
                <tr>
                    <td>
                        Optional
                    </td>
                    <td>
                        <code>id</code>, <code>label</code>, <code>hint</code>,
                        <code>disabled</code>, <code>required</code>, <code>accept</code>,
                        <code>maxSize</code>, <code>multiple</code>
                    </td>
                </tr>
            </table>
        </div>

        <x-code-example language="blade">
            @verbatim
                <x-inputs.file
                    accept="image/*,text/plain,application/pdf,application/msword,
                    application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    label="File Upload Drag & Drop"
                    maxSize="1"
                    multiple
                    name="attachments[]"
                />
            @endverbatim
        </x-code-example>

        <x-inputs.file
            accept=".jpeg,.jpg,image/*,.txt,text/plain,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            label="File Upload Drag & Drop"
            maxSize="1"
            multiple
            name="attachments[]"
        />
    @endcomponent

</section>
