<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Modal from "../../Components/Modal.vue";

const { householdName, householdProducts } = usePage().props;

//Sagrupēti produkti pēc kategorijas
const groupedProducts = computed(() => {
    return householdProducts.reduce((groups, item) => {
        const category = item.categoryName ?? 'Uncategorized'

        groups[category] ??= [] //ja nav kategorijas, tad izveido jaunu
        groups[category].push(item)

        return groups
    }, {})
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

            <div v-for="(products, category) in groupedProducts" :key="category">
                <h2>{{ category }}</h2>
                <ul>
                    <li v-for="product in products" :key="product.id">
                        {{ product.productName }} - {{ product.amount }} {{ product.unitName }}
                    </li>
                </ul>
            </div>
        </section>

    </MainLayout>
</template>
