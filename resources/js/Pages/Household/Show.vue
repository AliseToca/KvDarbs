<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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
</script>

<template>
    <MainLayout>
        <h1 class="capitalize">{{ householdName }}</h1>

        <div v-for="(products, category) in groupedProducts" :key="category">
            <h2>{{ category }}</h2>
            <ul>
                <li v-for="product in products" :key="product.id">
                    {{ product.productName }} - {{ product.amount }} {{ product.unitName }}
                </li>
            </ul>
        </div>
    </MainLayout>
</template>
