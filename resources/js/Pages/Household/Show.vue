<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import {computed, reactive, ref} from 'vue';
import Modal from "../../Components/Modal.vue";
import AcordionItem from "../../Components/AcordionItem.vue";
import InputField from "../../Components/Inputs/InputField.vue";
import HouseholdProducts from "../../Components/HouseholdProducts.vue";

const { householdName, householdProducts, productCategories, translations } = usePage().props;

const isModalOpen = ref(true);

const form = reactive({
    name: '',
    amount: 1,
    unit: 'g',
    expirationDate: '',
    errors: {},
});

//Sagrupēti produkti pēc kategorijas
const categorizedProducts = computed(() => {
    const map = {};

    //Ielasam visas produktu kateogrijas
    productCategories.forEach(category => {
        map[category.name] = [];
    })

    // Pievienojam katru produktu atbilstošajai kategorijai
    householdProducts.forEach(product => {
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
                <h1 class="capitalize">{{ householdName }}</h1>
                <button class="button primary" @click="isModalOpen = true">
                    <i class="pi pi-plus"></i>
                </button>
                <Modal v-model="isModalOpen">
                    <template #header>
                        <h2>{{translations.household.add_product}}</h2>
                    </template>

                    <template #body>
                        <form class="form-field" id="add-product-form" @submit.prevent="submit">
                            <InputField
                                v-model = "form.name"
                                class="form-field-item"
                                type="text"
                                id="name"
                                name="name"
                                :label="translations.fields.labels.name"
                                :error="form.errors.name"
                            />
                            <InputField
                                v-model="form.amount"
                                class="form-field-item"
                                type="number"
                                id="amount"
                                name="amount"
                                :label="translations.fields.labels.product.amount"
                                :error="form.errors.amount"
                            />
                            <InputField
                                v-model="form.unit"
                                class="form-field-item"
                                type="text"
                                id="unit"
                                name="unit"
                                :label="translations.fields.labels.product.unit"
                                :error="form.errors.unit"
                            />
                            <InputField
                                v-model="form.expirationDate"
                                class="form-field-item"
                                type="date"
                                id="expiration_date"
                                name="expiration_date"
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
