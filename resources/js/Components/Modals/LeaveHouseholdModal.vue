<script setup>
import {usePage, useForm} from '@inertiajs/vue3';
import Modal from './Modal.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
    leaveUrl: String,
});

const emit = defineEmits(['update:modelValue']);

function closeModal() {
    emit('update:modelValue', false);
}

const form = useForm({});

function confirmLeave() {
    form.post(props.leaveUrl, {
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
            <h2>Pamest mājsaimniecību</h2>
        </template>

        <template #body>
            <p>Vai tiešām vēlies pamest šo mājsaimniecību?</p>
        </template>

        <template #footer>
            <div class="button-container">
                <button class="button primary full-width" @click="confirmLeave" :disabled="form.processing">
                    Pamest
                </button>
                <button class="button full-width" @click="closeModal" :disabled="form.processing">
                    Atcelt
                </button>
            </div>
        </template>
    </Modal>
</template>
