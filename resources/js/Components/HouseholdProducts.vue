<script setup>
import {ref} from "vue";
import AcordionItem from "./AcordionItem.vue";
import FormatProduct from "./FormatProduct.vue";
import EditHouseholdProductModal from "./Modals/EditHouseholdProductModal.vue";

const props = defineProps({
    categorizedProducts: Object,
    noProductText: String
})

const isEditOpen = ref(false);
const selectedProduct = ref(null);

function openEdit(product) {
    selectedProduct.value = product;
    isEditOpen.value = true;
}
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
            <li v-for="product in products" :key="product.id" @click="openEdit(product)" class="product-item">
                <FormatProduct :name="product.productName" :amount="product.amount" :measurementTypeId="product.measurementTypeId"/>
                <span class="tag" v-if="product.expirationDate">
                    {{product.expirationDate}}
                </span>
            </li>
        </ul>

        <p v-else class="empty">
            {{ noProductText }}
        </p>
    </AcordionItem>

    <EditHouseholdProductModal
        v-model="isEditOpen"
        :product="selectedProduct"
    />
</template>
