<script setup>
import AcordionItem from "./AcordionItem.vue";

defineProps({
    categorizedProducts: Object,
    noProductText: String
})
</script>

<template>
    <AcordionItem
        v-for="(products, category) in categorizedProducts"
        :key="category"
    >
        <template #header>
            <h2>{{ category }}</h2>
        </template>

        <ul v-if="products.length">
            <li v-for="product in products" :key="product.id">
                {{ product.amount }}{{ product.unitName }} {{ product.productName }}
                <span class="tag">
                    {{product.expirationDate}}
                </span>
            </li>
        </ul>

        <p v-else>
            {{ noProductText }}
        </p>
    </AcordionItem>
</template>
