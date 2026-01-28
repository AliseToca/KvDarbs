<script setup>
import { computed, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Modal from './Modal.vue';
import InputField from "@/Components/Inputs/InputField.vue";
import SearchableSelect from "@/Components/Inputs/SearchableSelect.vue";

const props = defineProps({
    modelValue: Boolean,
    householdId: Number,
});

const emit = defineEmits(['update:modelValue']);

const { products, units, translations } = usePage().props;

const form = reactive({
    product_id: '',
    amount: 1,
    unit_id: '',
    expirationDate: '',
    errors: {},
});

// Filtrējam mērvienības pēc izvēlētā produkta
const filteredUnits = computed(() => {
    const product = products.find(p => p.id === form.product_id);
    if (!product) return [];

    return units.filter(
        u => u.measurement_type_id === product.measurement_type_id
    );
});

function close() {
    emit('update:modelValue', false);
}

function submit() {
    form.errors = {};

    router.post(route('household-products.store'), {
        household_id: props.householdId,
        product_id: form.product_id,
        amount: form.amount,
        unit_id: form.unit_id,
        expiration_date: form.expirationDate,
    }, {
        onSuccess: () => {
            close()
            router.visit(window.location.href, {
                preserveScroll: true,
                preserveState: false,
            })
        },
        onError: (errors) => {
            form.errors = errors
        }
    })
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="close">
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
                    v-model="form.expirationDate"
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
            >
                {{ translations.button.add }}
            </button>
        </template>
    </Modal>
</template>
