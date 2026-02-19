<script setup>
import {router, usePage, useForm} from '@inertiajs/vue3';
import Modal from './Modal.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
    product: Object,
});

// Nodod vecāka komponentei mainīgā vērtību
const emit = defineEmits(['update:modelValue']);

function closeModal() {
    emit('update:modelValue', false); //Aizver paziņojuma logu
}

const form = useForm({});

function confirmDelete() {
    if (!props.product?.id) return;

    form.delete(route('household-products.destroy', props.product.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ props.product?.productName }} Dzēšana</h2>
        </template>

        <template #body>
            <p>
                {{ translations.household.delete_message.ask_confirmation }}
                "<strong>
                    {{ props.product?.productName }}
                </strong>"
                {{ translations.household.delete_message.from_household }}
            </p>
        </template>

        <template #footer>
            <div class="button-container">
                <button class="button full-width" @click="closeModal" :disabled="form.processing">
                    {{ translations.button.cancel }}
                </button>
                <button class="button primary full-width" @click="confirmDelete" :disabled="form.processing">
                    {{ translations.button.delete }}
                </button>
            </div>
        </template>
    </Modal>
</template>
