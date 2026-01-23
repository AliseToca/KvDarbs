<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Modal from "../../Components/Modal.vue";

const { householdName, householdProducts, productCategories, translations } = usePage().props;

//Sagrupēti produkti pēc kategorijas
const categorizedProducts = computed(() => {
    const map = {}

    //Ielasam visas produktu kateogrijas
    productCategories.forEach(category => {
        map[category.name] = []
    })

    // Pievienojam katru produktu atbilstošajai kategorijai
    householdProducts.forEach(product => {
        const category = product.categoryName ?? 'Uncategorized'

        if (!map[category]) {
            map[category] = []
        }

        map[category].push(product)
    })

    return map
})


const isModalOpen = ref(false);
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
                    test
                </Modal>
            </header>

            <div v-for="(products, category) in categorizedProducts" :key="category">
                <h2>{{ category }}</h2>

                <ul v-if="products.length">
                    <li v-for="product in products" :key="product.id">
                        {{ product.productName }} - {{ product.amount }} {{ product.unitName }}
                    </li>
                </ul>

                <p v-else>
                    {{ translations.household.no_products }}
                </p>
            </div>
        </section>
    </MainLayout>
</template>
