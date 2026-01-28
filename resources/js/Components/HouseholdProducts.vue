<script setup>
import AcordionItem from "./AcordionItem.vue";
import FormatProduct from "./FormatProduct.vue";

const props = defineProps({
    categorizedProducts: Object,
    noProductText: String
})

console.log(props.categorizedProducts);
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
                <FormatProduct :name="product.productName" :amount="product.amount" :measurementTypeId="product.measurementTypeId"/>
                <span class="tag" v-if="product.expirationDate">
                    {{product.expirationDate}}
                </span>
            </li>
        </ul>

        <p v-else>
            {{ noProductText }}
        </p>
    </AcordionItem>
</template>
