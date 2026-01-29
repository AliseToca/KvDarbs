<script setup>
import { ref } from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import Modal from './Modal.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
    product: Object,
});

// Nodod vecāka komponentei mainīgā vērtību
const emit = defineEmits(['update:modelValue']);

const isSubmitting = ref(false);

function closeModal() {
    emit('update:modelValue', false); //Aizver paziņojuma logu
}

function confirmDelete() {
    if (!props.product?.id) return;

    isSubmitting.value = true;

    // Pieprasījums dzēst mājsaimniecības produktu
    router.delete(route('household-products.destroy', props.product.id), {
        onSuccess: () => {
            close();
            // Refresh page or preserve state
            router.visit(window.location.href, {
                preserveScroll: true,
                preserveState: false,
            });
        },
        onFinish: () => {
            isSubmitting.value = false;
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
                <button class="button full-width" @click="closeModal" :disabled="isSubmitting">
                    {{ translations.button.cancel }}
                </button>
                <button class="button primary full-width" @click="confirmDelete" :disabled="isSubmitting">
                    {{ translations.button.delete }}
                </button>
            </div>
        </template>
    </Modal>
</template>
