<script setup>
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import AcordionItem from "./AcordionItem.vue";
import EditHouseholdProductModal from "./Modals/EditHouseholdProductModal.vue";
import ConfirmDeleteModal from "./Modals/ConfirmDeleteModal.vue";
import ExpiryBadge from "./ExpiryBadge.vue";

const {translations} = usePage().props;

const props = defineProps({
    categorizedProducts: Object,
    noProductText: String
})

const isEditOpen = ref(false);
const isDeleteOpen = ref(false);
const selectedProduct = ref(null);

// Atvērt mājsaimniecības produkta rediģēšanas logu
function openEdit(entry, productName, measurementTypeId) {
    selectedProduct.value = {...entry, productName, measurementTypeId};
    isEditOpen.value = true;
}

// Atvērt mājsaimniecības produkta dzēšanas logu
function openDelete(entry, productName) {
    selectedProduct.value = {...entry, productName};
    isDeleteOpen.value = true;
}
</script>

<template>
    <!-- Aizveramais akordeons pēc mājsaimniecības produktu kategorijām -->
    <AcordionItem
        v-for="(products, category) in categorizedProducts"
        :key="category"
    >
        <!-- Mājsaimniecības produktu kategorijas nosaukums -->
        <template #header>
            <header class="category-header">
                <h2>{{ category }}</h2>
                <span class="product-count">
                    {{ products.length }}
                    {{
                        products.length === 1 ?
                            translations.household.products.singular :
                            translations.household.products.plural
                    }}
                </span>
            </header>
        </template>

        <!-- Mājsaimniecības produkti attiecīgajā kategorijā -->
        <ul v-if="products.length" class="product-list">
            <li v-for="product in products" :key="product.productId" class="product-card">
                <header class="product-card-header">
                    <span class="product-name">{{ product.productName }}</span>
                    <span class="product-total-badge">{{ product.totalAmount }}{{ product.unit }}</span>
                </header>

                <ul class="entry-list">
                    <li v-for="entry in product.entries" :key="entry.id" class="entry-row">
                        <button
                            class="entry-main"
                            @click="openEdit(entry, product.productName, product.measurementTypeId)"
                        >
                            <i class="pi pi-pencil entry-edit-icon"/>
                            <span class="entry-amount">{{ entry.amount }}{{ entry.unit }}</span>
                            <ExpiryBadge
                                v-if="entry.expiryBreakdown"
                                :breakdown="entry.expiryBreakdown"
                            />
                        </button>

                        <button
                            class="entry-delete"
                            @click.stop="openDelete(entry, product.productName)"
                            :aria-label="`Delete ${product.productName}`"
                        >
                            <i class="pi pi-trash" aria-hidden="true"/>
                        </button>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Ziņojums kad nav produktu kategorijā -->
        <p v-else class="empty">
            {{ noProductText }}
        </p>
    </AcordionItem>

    <!-- Paziņojuma logi -->
    <EditHouseholdProductModal
        v-model="isEditOpen"
        :product="selectedProduct"
        :key="selectedProduct?.id"
    />

    <ConfirmDeleteModal
        v-model="isDeleteOpen"
        :title="`${selectedProduct?.productName}`"
        :message="`${translations.household.delete_message.ask_confirmation} &quot;<strong>${selectedProduct?.productName}</strong>&quot; ${translations.household.delete_message.from_household}`"
        route-name="household-products.destroy"
        :route-param="selectedProduct?.id"
    />
</template>
