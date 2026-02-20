<script setup>
import Modal from "./Modal.vue";
import SearchableSelect from "../Inputs/SearchableSelect.vue";
import InputField from "../Inputs/InputField.vue";
import {useForm, usePage} from "@inertiajs/vue3";

const {translations} = usePage().props;

defineProps({
    household: Object,
})

const form = useForm({
    name: '',
    members: [],
});

</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{household.name}}</h2>
        </template>

        <template #body>
            <form id="edit-household-form" class="form-field" @submit.prevent="submit">
                <InputField
                    v-model="form.name"
                    type="text"
                    :label="translations.fields.labels.name"
                    :error="form.errors.name"
                    :max-length="30"
                />
            </form>
        </template>

        <template #footer>
            <button
                class="button primary full-width"
                form="edit-household-form"
                type="submit"
                :disabled="form.processing"
            >
                {{ translations.button.save }}
            </button>
        </template>
    </Modal>
</template>
