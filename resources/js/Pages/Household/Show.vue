<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {Link, router, useForm, usePage} from '@inertiajs/vue3';
import {computed, reactive, ref} from 'vue';
import InputField from "../../Components/Inputs/InputField.vue";
import HouseholdProducts from "../../Components/HouseholdProducts.vue";
import AddHouseholdProductModal from "../../Components/Modals/AddHouseholdProductModal.vue";

const { products, productCategories, units, translations } = usePage().props;

const householdProducts = computed(() => usePage().props.householdProducts);
const household = computed(() => usePage().props.household);

const isModalOpen = ref(false);

const form = useForm({
    product_id: '',
    amount: 1,
    unit_id: '',
    expiration_date: '',
    errors: {},
});

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
    householdProducts.value.forEach(product => {
        const category = product.categoryName ?? 'Uncategorized';

        if (!map[category]) {
            map[category] = [];
        }

        map[category].push(product);
    })

    return map;
});

function submit() {
    form.post(route('household-products.store'), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
}
</script>

<template>
    <MainLayout>
        <section class="household">
            <header>
                <h1 class="capitalize">{{ household.name }}</h1>
                <div>
                    <button class="button primary" @click="isModalOpen = true">
                        <i class="pi pi-plus"></i> Pievienot produktu
                    </button>
                    <Link
                        :href="route('household.edit', household.id)"
                        class="button"
                    >
                        <i class="pi pi-cog"></i>
                    </Link>

                </div>
            </header>

            <AddHouseholdProductModal
                v-model="isModalOpen"
                :household-id="household.id"
            />

            <HouseholdProducts :categorizedProducts="categorizedProducts" :no-product-text="translations.household.no_products"/>
        </section>
    </MainLayout>
</template>
