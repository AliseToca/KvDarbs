<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from './Modal.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
    recipe: Object,
});

const emit = defineEmits(['update:modelValue']);

function closeModal() {
    emit('update:modelValue', false);
}

const form = useForm({});

function confirmDelete() {
    if (!props.recipe?.slug) return;

    form.delete(route('recipe.delete', { recipe: props.recipe.slug }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ translations.recipe.delete.recipe_deletion }}</h2>
        </template>

        <template #body>
            <p>
                {{ translations.recipe.delete.delete_confirm }}
                "<strong>{{ props.recipe?.name }}</strong>"?
            </p>
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
