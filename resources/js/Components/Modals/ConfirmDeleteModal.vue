<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from './Modal.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
    title: String,
    message: String,
    routeName: String,
    routeParam: [String, Number, Object]
});

const emit = defineEmits(['update:modelValue', 'deleted']);

const form = useForm({});

function closeModal() {
    emit('update:modelValue', false);
}

function confirmDelete() {
    if (!props.routeParam) return;

    form.delete(route(props.routeName, props.routeParam), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            emit('deleted');
        },
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ title }} {{ translations.button.deletion }}</h2>
        </template>
        <template #body>
            <p v-html="message" />
        </template>
        <template #footer>
            <div class="button-container">
                <button class="button primary full-width" @click="confirmDelete" :disabled="form.processing">
                    {{ translations.button.delete }}
                </button>
                <button class="button full-width" @click="closeModal" :disabled="form.processing">
                    {{ translations.button.cancel }}
                </button>
            </div>
        </template>
    </Modal>
</template>
