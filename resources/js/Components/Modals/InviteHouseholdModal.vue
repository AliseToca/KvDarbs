<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from './Modal.vue';
import InputField from "@/Components/Inputs/InputField.vue";

const props = defineProps({
    modelValue: Boolean,
    household: Object,
});

const emit = defineEmits(['update:modelValue']);

const { translations } = usePage().props;

const form = useForm({
    email: '',
});

function closeModal() {
    emit('update:modelValue', false);
}

function submit() {
    form.post(route('households.invite.email.send', props.household.id), {
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
            <h2>Uzaicinājums uz "{{ household.name }}"</h2>
        </template>

        <template #body>
            <form id="invite-form" class="form-field" @submit.prevent="submit">
                <InputField
                    v-model="form.email"
                    type="email"
                    id="invite_email"
                    name="email"
                    :label="translations.auth.email"
                    :error="form.errors.email"
                />
            </form>
        </template>

        <template #footer>
            <button
                class="button primary full-width"
                form="invite-form"
                type="submit"
                :disabled="form.processing"
            >
                Nosūtīt uzaicinājumu
            </button>
        </template>
    </Modal>
</template>
