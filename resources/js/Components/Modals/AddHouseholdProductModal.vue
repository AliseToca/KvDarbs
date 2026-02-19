<script setup>
import { computed, reactive } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Modal from './Modal.vue';
import InputField from "@/Components/Inputs/InputField.vue";
import SearchableSelect from "@/Components/Inputs/SearchableSelect.vue";

const props = defineProps({
    modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const { products, units, translations } = usePage().props;

const form = useForm({
    product_id: '',
    amount: 1,
    unit_id: '',
    expiration_date: '',
});

// Iegūst atbilstošās mērvienības
const filteredUnits = computed(() => {
    const product = products.find(p => p.id === form.product_id);
    if (!product) return [];

    return units.filter(
        u => u.measurement_type_id === product.measurement_type_id
    );
});

function closeModal() {
    emit('update:modelValue', false);
}

function submit() {
    form.post(route('household-products.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset({ amount: 1 });
            router.reload({ only: ['householdProducts'] });
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
            <h2>{{ translations.household.add_product }}</h2>
        </template>

        <template #body>
            <form id="add-product-form" class="form-field" @submit.prevent="submit">
                <SearchableSelect
                    v-model="form.product_id"
                    :items="products"
                    :label="translations.fields.labels.name"
                    :placeholderValue="translations.household.search_products"
                    :notFoundMessage="translations.fields.labels.product.not_found"
                />

                <InputField
                    v-model="form.amount"
                    type="number"
                    :label="translations.fields.labels.product.amount"
                    :error="form.errors.amount"
                />

                <SearchableSelect
                    v-model="form.unit_id"
                    :items="filteredUnits"
                    :label="translations.fields.labels.product.unit"
                    :clearable="false"
                />

                <InputField
                    v-model="form.expiration_date"
                    type="date"
                    :label="translations.fields.labels.product.expiration_date"
                    :error="form.errors.expiration_date"
                />
            </form>
        </template>

        <template #footer>
            <button
                class="button primary full-width"
                form="add-product-form"
                type="submit"
                :disabled="form.processing"
            >
                {{ translations.button.add }}
            </button>
        </template>
    </Modal>
</template>
