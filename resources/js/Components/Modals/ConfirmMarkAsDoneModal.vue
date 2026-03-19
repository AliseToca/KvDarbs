<script setup>
import {router, useForm, usePage} from '@inertiajs/vue3';
import Modal from './Modal.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
    title: String,
    message: String,
    recipeId: Number,
});

const emit = defineEmits(['update:modelValue', 'deleted']);

const form = useForm({});

function closeModal() {
    emit('update:modelValue', false);
}

function markAsDone() {
    form.post(route('recipes.mark-as-done', { recipe: props.recipeId }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ title }}</h2>
        </template>
        <template #body>
            <p v-html="message"/>
        </template>
        <template #footer>
            <div class="button-container">
                <button class="button primary full-width" @click="markAsDone" :disabled="form.processing">
                    {{translations.button.yes}}
                </button>
                <button class="button full-width" @click="closeModal" :disabled="form.processing">
                    {{ translations.button.no }}
                </button>
            </div>
        </template>
    </Modal>
</template>
