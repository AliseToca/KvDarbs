<script setup>
import { reactive, watch, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import Modal from "./Modal.vue";
import InputField from "../Inputs/InputField.vue";
import SearchableSelect from "../Inputs/SearchableSelect.vue";

const props = defineProps({
    modelValue: Boolean,
    product: Object,
});

const emit = defineEmits(["update:modelValue"]);

const { units, translations } = usePage().props;

const form = reactive({
    amount: 1,
    unit_id: '',
    expirationDate: '',
    errors: {},
});

const availableUnits = computed(() => {
    if (!props.product) return [];
    return units
        .filter(u => u.measurement_type_id === props.product.measurementTypeId)
        .sort((a, b) => b.conversion_factor - a.conversion_factor);
});

const bestUnit = computed(() => {
    if (!props.product) return null;
    return availableUnits.value.find(u => props.product.amount >= u.conversion_factor)
        ?? availableUnits.value.at(-1);
});

watch(() => props.product, (p) => {
    if (!p) return;

    const unit = bestUnit.value;

    form.unit_id = unit?.id || '';
    form.amount = unit ? p.amount / unit.conversion_factor : p.amount;
    form.expirationDate = p.expirationDate;
}, { immediate: true });

function close() {
    emit("update:modelValue", false);
}

function submit() {
    form.errors = {};

    if (!props.product?.id) {
        console.warn("No product selected, cannot submit");
        return;
    }

    const unit = units.find(u => u.id === form.unit_id);


    router.put(route('household-products.update', props.product.id), {
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
    });
}
</script>

<template>
    <Modal :model-value="modelValue" @update:modelValue="close">
        <template #header>
            <h2>{{ translations.household.edit_product }} {{ props.product?.productName }}</h2>
        </template>

        <template #body>
            <form id="edit-product-form" class="form-field" @submit="submit">
                <InputField
                    v-model="form.amount"
                    type="number"
                    :label="translations.fields.labels.product.amount"
                    :error="form.errors.amount"
                />

                <SearchableSelect
                    v-model="form.unit_id"
                    :items="availableUnits"
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
                form="edit-product-form"
                type="submit"
            >
                {{ translations.button.save }}
            </button>
        </template>
    </Modal>
</template>
