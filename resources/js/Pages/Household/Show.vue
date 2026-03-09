<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {Link, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import HouseholdProducts from "../../Components/HouseholdProducts.vue";
import AddHouseholdProductModal from "../../Components/Modals/AddHouseholdProductModal.vue";
import InviteHouseholdModal from "../../Components/Modals/InviteHouseholdModal.vue";
import LeaveHouseholdModal from "../../Components/Modals/LeaveHouseholdModal.vue";
import Dropdown from "../../Components/Dropdown.vue";

const {productCategories, translations, userRole, householdUsersCount } = usePage().props;

const householdProducts = computed(() => usePage().props.householdProducts);
const household = computed(() => usePage().props.household);

const isAddProductModalOpen = ref(false);
const isInviteModalOpen = ref(false);
const isLeaveModalOpen = ref(false);

const dropdown = ref(null);

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
            <header>
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
                    <Dropdown ref="dropdown">
                        <template #trigger>
                            <button class="button">
                                <i class="pi pi-ellipsis-v"></i>
                            </button>
                        </template>

                        <li v-if="userRole === 'owner'">
                            <button @click="dropdown.close(); isInviteModalOpen = true">
                                <i class="pi pi-user-plus"/>
                                Uzaicini lietotāju
                            </button>
                        </li>
                        <li v-if="userRole === 'owner'">
                            <Link :href="route('households.edit', household.id)" @click="dropdown?.close()">
                                <i class="pi pi-cog"/>
                                Iestatījumi
                            </Link>
                        </li>
                        <li>
                            <button @click="dropdown?.close(); isLeaveModalOpen = true">
                                <i class="pi pi-sign-out"/>
                                {{ translations.household.leave }}
                            </button>
                        </li>
                    </Dropdown>
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
                :household-id="household.id"
            />

            <HouseholdProducts :categorizedProducts="categorizedProducts" :no-product-text="translations.household.no_products"/>
        </section>
    </MainLayout>
</template>
