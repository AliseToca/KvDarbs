<script setup>
import Modal from "./Modal.vue";
import InputField from "../Inputs/InputField.vue";
import { watch } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    modelValue: Boolean,
    household: Object,
});

const { translations } = usePage().props;

const emit = defineEmits(["update:modelValue"]);

const form = useForm({
    name: '',
});

watch(() => props.household, (household) => {
    if (!household) return;

    form.name = household.name;
}, { immediate: true });

function closeModal() {
    emit("update:modelValue", false);
}

function submit() {
    if (!props.household?.id) return;

    form.put(route('households.update', props.household.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            router.reload({ only: ['households'], preserveScroll: true });
        },
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ translations.household.ur_household }}</h2>
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
