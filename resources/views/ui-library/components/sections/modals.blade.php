<section class="ui-section" id="modals">
    <h2 class="ui-h2">Modals</h2>
    <p>Modal content can be loaded either from a DOM template (static) or from the backend (dynamic).</p>
    @component('ui-library.components.part.instructions-block')
        <h2 class="ui-h4">Options</h4>
            <p>The modal constructor accepts a <code>ModalOptions</code> object that holds these properties.</p>
            <table>
                <tr>
                    <th>Property</th>
                    <th>Type</th>
                    <th>Description</th>
                <tr>
                    <td><code>contentUrl</code></td>
                    <td>string</td>
                    <td>The url for the modal content HTML (if content is loaded from remote).</td>
                </tr>
                <tr>
                    <td><code>templateName</code></td>
                    <td>string</td>
                    <td>The name of the modal template (if content is loaded from DOM template).</td>
                </tr>
                <tr>
                    <td><code>afterOpen</code></td>
                    <td>function</td>
                    <td>A callback to be executed after the modal is opened.</td>
                </tr>
                <tr>
                    <td><code>afterClose</code></td>
                    <td>function</td>
                    <td>A callback to be executed after the modal is closed.</td>
                </tr>
            </table>
        @endcomponent

        @component('ui-library.components.part.instructions-block')
            <h2 class="ui-h4">Methods</h4>
                <p>The modal instance provides these public methods.</p>

                <table>
                    <tr>
                        <th>Method</th>
                        <th>Description</th>
                    </tr>
                    <tr>
                        <td><code>setTrigger()</code></td>
                        <td>Binds the <code>click</code> event listener to the trigger element.<br> Loads modal url/template name (if provided) from the trigger element.</td>

                    </tr>
                    <tr>
                        <td><code>injectContent()</code></td>
                        <td>Injects HTML content into the <code>modal-content</code> element.</td>
                    </tr>
                    <tr>
                        <td><code>show()</code></td>
                        <td>Shows the modal.</td>
                    </tr>
                    <tr>
                        <td><code>hide()</code></td>
                        <td>Hides the modal.</td>
                    </tr>
                </table>
            @endcomponent

            <h2 class="ui-h3">Static modal</h3>
                @component('ui-library.components.part.instructions-block')
                    <div>
                        <p>Prepare a modal content template using the <code>x-modal.template</code> component and specify a modal ID. Add this template ot the DOM.</p>

                        <x-code-example language="blade">
                            @verbatim
                                <x-modal.template id="static-modal">
                                    <div class="demo-modal">
                                        <h2 class="ui-h2" class="modal-title">Demo modal</h2>
                                        <p>Lorem ipsum dolor sit amet...</p>
                                    </div>
                                </x-modal.template>
                            @endverbatim
                        </x-code-example>
                    </div>

                    <div>
                        <p>Then, add the same id to the trigger element's <code>data-modal-id</code> attribute.</p>

                        <x-code-example language="blade">
                            @verbatim
                                <x-button
                                    data-show-static-modal
                                    data-modal-id="static-modal"
                                    text="Show static modal"
                                />
                            @endverbatim
                        </x-code-example>
                    </div>

                    <div>
                        <p>Now you can initialize the modal instance with the <code>setTrigger()</code> method. This will bind the <code>click</code> event listener to the trigger element and load the template, using the id from the trigger element.</p>

                        <x-code-example language="ts">
                            <pre>
                (new Modal).setTrigger(
                    document.querySelector('[data-show-static-modal]')
                );
                </pre>
                        </x-code-example>
                    </div>
                    <x-button
                        data-show-static-modal
                        data-modal-id="static-modal"
                        text="Show static modal"
                    />
                @endcomponent

                <h2 class="ui-h3">Dynamic modal</h3>
                    @component('ui-library.components.part.instructions-block')
                        <div>
                            <p>Prepare an endpoint that will return the modal content in the <code>html</code> property of a JSON object.</p>

                            <x-code-example language="ts">
                                <pre>
                public function getDemoModal(): JsonResponse {
                    return response()->json([
                        'html' => view('ui-library.components.part.demo-modal')->render(),
                    ]);
                }
                </pre>
                            </x-code-example>
                        </div>
                        <div>
                            <p>Set the endpoint url in the <code>data-modal-url</code> attribute of the trigger element.</p>

                            <x-code-example language="blade">
                                @verbatim
                                    <x-button
                                        data-show-dynamic-modal
                                        data-modal-url="{{ route('components.get-demo-modal') }}"
                                        text="Show dynamic modal"
                                    />
                                @endverbatim
                            </x-code-example>
                        </div>
                        <div>
                            <p>Initialize the modal with the <code>setTrigger()</code> method. This will bind the <code>click</code> event listener to the trigger element and load the html from the endpoint.</p>

                            <x-code-example language="ts">
                                <pre>
                (new Modal).setTrigger(
                    document.querySelector('[data-show-dynamic-modal]')
                );
                </pre>
                            </x-code-example>
                        </div>
                        <x-button
                            data-show-dynamic-modal
                            data-modal-url="{{ route('components.get-demo-modal') }}"
                            text="Show dynamic modal"
                        />
                    @endcomponent

                    @include('ui-library.components.part.modal-static')
</section>
