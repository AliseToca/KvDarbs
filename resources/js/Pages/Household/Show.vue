<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {router, usePage} from '@inertiajs/vue3';
import {computed, reactive, ref} from 'vue';
import Modal from "../../Components/Modal.vue";
import AcordionItem from "../../Components/AcordionItem.vue";
import InputField from "../../Components/Inputs/InputField.vue";
import HouseholdProducts from "../../Components/HouseholdProducts.vue";
import SearchableSelect from "../../Components/Inputs/SearchableSelect.vue";

const { household, householdProducts, products, productCategories, units, translations } = usePage().props;

const isModalOpen = ref(false);

const form = reactive({
    product_id: '',
    amount: 1,
    unit_id: '',
    expirationDate: '',
    errors: {},
});

//
const filteredUnits = computed(() => {
    if (!form.product_id) return [];

    const product = products.find(product => product.id === form.product_id);

    if (!product) return [];

    return units.filter(unit => unit.measurement_type_id === product.measurement_type_id);
});

//Sagrupēti produkti pēc kategorijas
const categorizedProducts = computed(() => {
    const map = {};

    //Ielasam visas produktu kateogrijas
    productCategories.forEach(category => {
        map[category.name] = [];
    })

    //Pievienojam katru produktu atbilstošajai kategorijai
    householdProducts.forEach(product => {
        const category = product.categoryName ?? 'Uncategorized';

        if (!map[category]) {
            map[category] = [];
        }

        map[category].push(product);
    })

    return map;
});

function submit() {
    form.errors = {};

    router.post(route('household-products.store'), {
        household_id: household.id,
        product_id: form.product_id,
        amount: form.amount,
        unit_id: form.unit_id,
        expiration_date: form.expirationDate,
    }, {
        onSuccess: () => {
            isModalOpen.value = false;
            router.visit(window.location.href, {
                preserveScroll: true,
                preserveState: false,
            });
        },
        onError: (errors) => {
            form.errors = errors;
        }
    });
}

</script>

<template>
    <MainLayout>
        <section class="household">
            <header>
                <h1 class="capitalize">{{ household.name }}</h1>
                <button class="button primary" @click="isModalOpen = true">
                    <i class="pi pi-plus"></i>
                </button>
                <Modal v-model="isModalOpen">
                    <template #header>
                        <h2>{{translations.household.add_product}}</h2>
                    </template>

                    <template #body>
                        <form class="form-field" id="add-product-form" @submit.prevent="submit">
                            <SearchableSelect
                                v-model="form.product_id"
                                :items="products"
                                id="product"
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
                                id="unit"
                                :label="translations.fields.labels.product.unit"
                                :placeholderValue="translations.household.search_units"
                                :notFoundMessage="translations.fields.labels.product.not_found"
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
                        <button class="button primary full-width" form="add-product-form" type="submit">
                            {{ translations.button.add }}
                        </button>
                    </template>
                </Modal>
            </header>

            <HouseholdProducts :categorizedProducts="categorizedProducts" :no-product-text="translations.household.no_products"/>
        </section>
    </MainLayout>
</template>
