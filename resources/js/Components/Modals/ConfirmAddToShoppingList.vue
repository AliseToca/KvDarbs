<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
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

function confirmFromRecipeHousehold() {
    form.post(route('shopping-list.add-from-recipe-household', { recipe: props.recipeId }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
}

function confirmFromRecipe() {
    form.post(route('shopping-list.add-from-recipe', { recipe: props.recipeId }), {
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
            <p v-html="message" />
        </template>
        <template #footer>
            <div class="button-container">
                <button class="button primary full-width" @click="confirmFromRecipeHousehold" :disabled="form.processing">
                    {{translations.button.yes}}
                </button>
                <button class="button full-width" @click="confirmFromRecipe" :disabled="form.processing">
                    {{ translations.button.no }}
                </button>
            </div>
        </template>
    </Modal>
</template>
