<script setup>
import { usePage, useForm } from '@inertiajs/vue3';
import Modal from './Modal.vue';
import InputField from '../Inputs/InputField.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

function closeModal() {
    emit('update:modelValue', false);
}

const form = useForm({
    password: '',
});

function confirmDelete() {
    form.post(route('profile.destroy'), {
        preserveScroll: true,
        onError: () => {
            form.reset('password');
        },
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ translations.auth.account}} Dzēšana</h2>
        </template>

        <template #body>
            <form class="form-field" @submit.prevent="confirmDelete">
                <InputField
                    v-model="form.password"
                    type="password"
                    :label="translations.auth.password"
                    :error="form.errors.password"
                />
            </form>
        </template>

        <template #footer>
            <div class="button-container">
                <button class="button primary full-width" @click="confirmDelete" :disabled="form.processing || !form.password">
                    {{ translations.button.delete }}
                </button>

                <button class="button full-width" @click="closeModal" :disabled="form.processing">
                    {{ translations.button.cancel }}
                </button>
            </div>
        </template>
    </Modal>
</template>
