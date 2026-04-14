<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import HouseholdProducts from "../../Components/HouseholdProducts.vue";
import AddHouseholdProductModal from "../../Components/Modals/AddHouseholdProductModal.vue";
import InviteHouseholdModal from "../../Components/Modals/InviteHouseholdModal.vue";
import LeaveHouseholdModal from "../../Components/Modals/LeaveHouseholdModal.vue";
import HouseholdActionsDropdown from "../../Components/Dropdowns/HouseholdActionsDropdown.vue";

const {productCategories, translations, householdUsersCount } = usePage().props;

const householdProducts = computed(() => usePage().props.householdProducts);
const household = computed(() => usePage().props.household);

const isAddProductModalOpen = ref(false);
const isInviteModalOpen = ref(false);
const isLeaveModalOpen = ref(false);

//Sagrupēti produkti pēc kategorijas
const categorizedProducts = computed(() => {
    const map = {};

    //Ielasam visas produktu kateogrijas
    productCategories.forEach(category => {
        map[category.name] = [];
    })

    //Pievienojam katru produktu atbilstošajai kategorijai
    householdProducts.value.forEach(product => {
        const category = product.categoryName ?? 'Uncategorized';

        if (!map[category]) {
            map[category] = [];
        }

        map[category].push(product);
    })

    return map;
});
</script>

<template>
    <MainLayout>
        <section class="household">
            <header class="household-header">
                <div class="household-title">
                    <h1 class="capitalize">{{ household.name }}</h1>
                    <div>
                        <i class="pi pi-users"/>
                        <span>{{householdUsersCount}}</span>
                    </div>
                </div>
                <div class="actions">
                    <button class="button primary" @click="isAddProductModalOpen = true">
                        <i class="pi pi-plus"></i> {{ translations.household.add_product }}
                    </button>

                    <HouseholdActionsDropdown
                        :household="household"
                        @invite="isInviteModalOpen = true"
                        @leave="isLeaveModalOpen = true"
                    />
                </div>
            </header>

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

            <HouseholdProducts :categorizedProducts="categorizedProducts" :no-product-text="translations.household.no_products"/>
        </section>
    </MainLayout>
</template>
