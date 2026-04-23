<script setup>
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";
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
    <div class="household-products">
        <section
            v-for="(products, category) in categorizedProducts"
            :key="category"
            class="category"
        >
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

            <ul v-if="products.length" class="category-products-list">
                <li v-for="product in products" :key="product.productId" class="product-card">
                    <div class="product-card-header">
                        <span class="product-card-name">{{ product.productName }}</span>
                        <span class="product-card-total-badge">{{ product.totalAmount }}{{ product.unit }}</span>
                    </div>

                    <ul class="product-card-list">
                        <li v-for="entry in product.entries" :key="entry.id" class="product-card-list-row">
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
                            >
                                <i class="pi pi-trash"/>
                            </button>
                        </li>
                    </ul>
                </li>
            </ul>

            <ul v-else>
                <li class="product-card">
                    {{ noProductText }}
                </li>
            </ul>
        </section>

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
    </div>
</template>
