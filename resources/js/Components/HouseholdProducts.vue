<script setup>
import {ref} from "vue";
import AcordionItem from "./AcordionItem.vue";
import EditHouseholdProductModal from "./Modals/EditHouseholdProductModal.vue";
import DeleteHouseholdProductModal from "./Modals/DeleteHouseholdProductModal.vue";

const props = defineProps({
    categorizedProducts: Object,
    noProductText: String
})

const isEditOpen = ref(false);
const isDeleteOpen = ref(false);
const selectedProduct = ref(null);

// Atvērt mājsaimniecības produkta rediģēšanas logu
function openEdit(product) {
    selectedProduct.value = product;
    isEditOpen.value = true;
}

// Atvērt mājsaimniecības produkta dzēšanas logu
function openDelete(product) {
    selectedProduct.value = product;
    isDeleteOpen.value = true;
}
</script>

<template>
    <!-- Aizveramais akordeons pēc mājsaimniecības produktu kategorijām -->
    <AcordionItem
        v-for="(products, category) in categorizedProducts"
        :key="category"
    >
        <!-- Mājsaimniecībsa produktu kategorijas nosaukums -->
        <template #header>
            <h2>{{ category }}</h2>
        </template>

        <!-- Mājsaimniecības produkti attiecīgajā kateogrijā -->
        <ul v-if="products.length">
            <li v-for="product in products" :key="product.id">
                <div class="product-item" @click="openEdit(product)" >
                    <div class="product-item-inner">
                        <i class="pi pi-pencil"></i>

                        <span>
                            {{ product.amount }}{{ product.unit }} {{ product.productName }}
                        </span>

                        <span class="tag" v-if="product.expirationDate">
                            {{product.expirationDate}}
                        </span>
                    </div>
                </div>

                <i class="pi pi-trash" @click.stop="openDelete(product)"></i>
            </li>
        </ul>

        <!-- Ziņojums kas nav prodktu kategorijā -->
        <p v-else class="empty">
            {{ noProductText }}
        </p>
    </AcordionItem>

    <!-- Paziņojuma logi  -->
    <EditHouseholdProductModal
        v-model="isEditOpen"
        :product="selectedProduct"
    />

    <DeleteHouseholdProductModal
        v-model="isDeleteOpen"
        :product="selectedProduct"
    />
</template>
