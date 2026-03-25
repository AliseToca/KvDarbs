<script setup>
import Modal from './Modal.vue';
import InputField from "@/Components/Inputs/InputField.vue";
import { useForm, usePage } from '@inertiajs/vue3';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const form = useForm({
    name: '',
});

function closeModal() {
    emit('update:modelValue', false);
}

function submit() {
    form.post(route('folders.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        }
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ translations.folders.create_folder }}</h2>
        </template>

        <template #body>
            <form id="create-folder-form" class="form-field" @submit.prevent="submit">
                <InputField
                    v-model="form.name"
                    type="text"
                    :label="translations.fields.labels.name"
                    :error="form.errors.name"
                    :max-length="50"
                />
            </form>
        </template>

        <template #footer>
            <button
                class="button primary full-width"
                form="create-folder-form"
                type="submit"
                :disabled="form.processing"
            >
                {{ translations.button.create }}
            </button>
        </template>
    </Modal>
</template>
