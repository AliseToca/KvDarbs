<script setup>
import { computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Modal from './Modal.vue';
import InputField from "@/Components/Inputs/InputField.vue";
import SearchableSelect from "@/Components/Inputs/SearchableSelect.vue";

// Global page props: product catalog, available units, and i18n strings
const { products, units, translations } = usePage().props;

const props = defineProps({
    // Controls modal visibility via v-model
    modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

// Form state managed by Inertia — tracks fields, errors, and processing state
const form = useForm({
    product_id: '',
    amount: 1,
    unit_id: '',
    expiration_date: '',
});

// Only show units that match the selected product's measurement type
const filteredUnits = computed(() => {
    const product = products.find(p => p.id === form.product_id);
    if (!product) return [];

    return units.filter(u => u.measurement_type_id === product.measurement_type_id);
});

// Reset unit selection whenever the product changes to avoid unit/type mismatch
watch(() => form.product_id, () => {
    form.unit_id = '';
});

// Emit false to let the parent close the modal via v-model
function closeModal() {
    emit('update:modelValue', false);
}

function submit() {
    form.post(route('household-products.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();   // Clear fields before closing so modal reopens fresh
            closeModal();
            router.reload({ only: ['householdProducts'] }); // Sync list without full page reload
        },
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
        <template #header>
            <h2>{{ translations.household.add_product }}</h2>
        </template>

        <template #body>
            <!-- form id is referenced by the submit button in #footer (outside this form element) -->
            <form id="add-product-form" class="form-field" @submit.prevent="submit">
                <!-- Product picker — drives unit filtering below -->
                <SearchableSelect
                    v-model="form.product_id"
                    :items="products"
                    :label="translations.fields.labels.name"
                    :placeholderValue="translations.household.search_products"
                    :notFoundMessage="translations.fields.labels.product.not_found"
                    :error="form.errors.product_id"
                />

                <InputField
                    v-model="form.amount"
                    type="number"
                    :label="translations.fields.labels.product.amount"
                    :error="form.errors.amount"
                />

                <!-- Unit picker — disabled until a product is selected; options filtered by measurement type -->
                <SearchableSelect
                    v-model="form.unit_id"
                    :items="filteredUnits"
                    :label="translations.fields.labels.product.unit"
                    :clearable="false"
                    :disabled="!form.product_id"
                    :disabledPlaceholder="translations.household.select_product_first"
                    :error="form.errors.unit_id"
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
            <!-- Linked to the form above via the `form` attribute since it lives outside the <form> element -->
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
