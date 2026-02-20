<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {Link, router, useForm, usePage} from '@inertiajs/vue3';
import {computed, reactive, ref} from 'vue';
import InputField from "../../Components/Inputs/InputField.vue";
import HouseholdProducts from "../../Components/HouseholdProducts.vue";
import AddHouseholdProductModal from "../../Components/Modals/AddHouseholdProductModal.vue";
import EditHouseholdModal from "../../Components/Modals/EditHouseholdModal.vue";

const { products, productCategories, units, translations } = usePage().props;

const householdProducts = computed(() => usePage().props.householdProducts);
const household = computed(() => usePage().props.household);

const isAddProductModalOpen = ref(false);
const isEditHouseholdModalOpen = ref(false);

const form = useForm({
    product_id: '',
    amount: 1,
    unit_id: '',
    expiration_date: '',
    errors: {},
});

//Sagrupēti produkti pēc kategorijas
const categorizedProducts = computed(() => {
    const map = {};

    //Ielasam visas produktu kateogrijas
    productCategories.forEach(category => {
        map[category.name] = [];
    })

    //Pievienojam katru produktu atbilstošajai kategorijai
    householdProducts.value.forEach(product => {
        const category = product.categoryName ?? 'Uncategorized';

        if (!map[category]) {
            map[category] = [];
        }

        map[category].push(product);
    })

    return map;
});
</script>

<template>
    <MainLayout>
        <section class="household">
            <header>
                <h1 class="capitalize">{{ household.name }}</h1>
                <div>
                    <button class="button primary" @click="isAddProductModalOpen = true">
                        <i class="pi pi-plus"></i> Pievienot produktu
                    </button>
                    <button class="button" @click="isEditHouseholdModalOpen = true">
                        <i class="pi pi-cog"></i>
                    </button>
                </div>
            </header>

            <AddHouseholdProductModal
                v-model="isAddProductModalOpen"
                :household-id="household.id"
            />

            <EditHouseholdModal
                v-model="isEditHouseholdModalOpen"
                :household="household"
            />

            <HouseholdProducts :categorizedProducts="categorizedProducts" :no-product-text="translations.household.no_products"/>
        </section>
    </MainLayout>
</template>
