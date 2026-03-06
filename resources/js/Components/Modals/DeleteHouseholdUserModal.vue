<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from './Modal.vue';

const { translations } = usePage().props;

const props = defineProps({
    modelValue: Boolean,
    member: Object,
    household: Object,
});

const emit = defineEmits(['update:modelValue']);

function closeModal() {
    emit('update:modelValue', false);
}

const form = useForm({});

function confirmDelete() {
    if (!props.member?.id) return;

    form.delete(route('households.users.destroy', { household: props.household.id, user: props.member.id }), {
        preserveScroll: true,
        only: ['household_users'],
        onSuccess: () => closeModal(),
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>Lietotāja noņemšana</h2>
        </template>

        <template #body>
            <p>
                Vai esi pārliecināts, ka vēliens noņemt
                "<strong>@{{ props.member?.username }}</strong>" no mājsaimniecības?
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
