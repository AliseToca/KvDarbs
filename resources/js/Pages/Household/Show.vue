<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import HouseholdProducts from "../../Components/HouseholdProducts.vue";
import AddHouseholdProductModal from "../../Components/Modals/AddHouseholdProductModal.vue";
import InviteHouseholdModal from "../../Components/Modals/InviteHouseholdModal.vue";
import LeaveHouseholdModal from "../../Components/Modals/LeaveHouseholdModal.vue";
import HouseholdActionsDropdown from "../../Components/Dropdowns/HouseholdActionsDropdown.vue";

// Static page props — these don't change after the initial page load
const { productCategories, translations, householdUsersCount } = usePage().props;

// Reactive props — re-evaluated after partial Inertia reloads (e.g. after adding a product)
const householdProducts = computed(() => usePage().props.householdProducts);
const household = computed(() => usePage().props.household);

// Modal visibility flags
const isAddProductModalOpen = ref(false);
const isInviteModalOpen = ref(false);
const isLeaveModalOpen = ref(false);

// Groups household products by their category name.
// All known categories are pre-seeded as empty arrays so they always appear in the list,
// even if no products belong to them. Products without a category fall into 'Uncategorized'.
const categorizedProducts = computed(() => {
    const map = {};

    // Pre-seed every category so empty ones are still rendered
    productCategories.forEach(category => {
        map[category.name] = [];
    });

    // Place each product under its category, creating a bucket if needed
    householdProducts.value.forEach(product => {
        const category = product.categoryName ?? 'Uncategorized';

        if (!map[category]) {
            map[category] = [];
        }

        map[category].push(product);
    });

    return map;
});
</script>

<template>
    <MainLayout>
        <section class="household">
            <header class="household-header">
                <div class="household-title">
                    <h1 class="capitalize">{{ household.name }}</h1>
                    <!-- Member count badge -->
                    <div>
                        <i class="pi pi-users"/>
                        <span>{{ householdUsersCount }}</span>
                    </div>
                </div>

                <div class="actions">
                    <button class="button primary" @click="isAddProductModalOpen = true">
                        <i class="pi pi-plus"></i> {{ translations.household.add_product }}
                    </button>

                    <!-- Dropdown with invite / leave actions -->
                    <HouseholdActionsDropdown
                        :household="household"
                        @invite="isInviteModalOpen = true"
                        @leave="isLeaveModalOpen = true"
                    />
                </div>
            </header>

            <!-- Modals are mounted here but stay hidden until their flag is set to true -->
            <InviteHouseholdModal
                v-model="isInviteModalOpen"
                :household="household"
            />

            <LeaveHouseholdModal
                v-model="isLeaveModalOpen"
                :household="household"
            />

            <AddHouseholdProductModal
                v-model="isAddProductModalOpen"
            />

            <!-- Renders products grouped by category -->
            <HouseholdProducts
                :categorizedProducts="categorizedProducts"
                :no-product-text="translations.household.no_products"
            />
        </section>
    </MainLayout>
</template>
