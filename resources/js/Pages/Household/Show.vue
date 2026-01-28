<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {router, usePage} from '@inertiajs/vue3';
import {computed, reactive, ref} from 'vue';
import InputField from "../../Components/Inputs/InputField.vue";
import HouseholdProducts from "../../Components/HouseholdProducts.vue";
import AddHouseholdProductModal from "../../Components/Modals/AddHouseholdProductModal.vue";

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
            </header>

            <AddHouseholdProductModal
                v-model="isModalOpen"
                :household-id="household.id"
            />

            <HouseholdProducts :categorizedProducts="categorizedProducts" :no-product-text="translations.household.no_products"/>
        </section>
    </MainLayout>
</template>
