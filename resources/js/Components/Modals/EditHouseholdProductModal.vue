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

const { units, translations } = usePage().props;

// Nodod vecāka komponentei mainīgā vērtību
const emit = defineEmits(["update:modelValue"]);

const form = reactive({
    amount: 1,
    unit_id: '',
    expirationDate: '',
    errors: {},
});

// Pieejamās mērvienības konkrētajam produktam
const availableUnits = computed(() => {
    if (!props.product) return [];

    return units
        .filter(unit => unit.measurement_type_id === props.product.measurementTypeId)
        .sort((a, b) => b.conversion_factor - a.conversion_factor); //Lielākās -> mazāko
});

// Automātiski aizpilda formas vērtibas
watch(() => props.product, (product) => {
    if (!product) return;

    form.unit_id = product.unitId;
    form.amount = product.amount;
    form.expirationDate = product.expirationDate;
}, { immediate: true });


function closeModal() {
    emit("update:modelValue", false);
}

function submit() {
    form.errors = {};

    if (!props.product?.id) {
        return;
    }

    const unit = units.find(u => u.id === form.unit_id);

    //Pieprasījums atjaunināt mājsaimniecības produkta vērtibas
    router.put(route('household-products.update', props.product.id), {
        amount: form.amount,
        unit_id: form.unit_id,
        expiration_date: form.expirationDate,
    }, {
        onSuccess: () => {
            closeModal()
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
    <Modal :model-value="modelValue" @update:modelValue="closeModal">
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
