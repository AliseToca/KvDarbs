<script setup>
import {ref} from 'vue';
import Dropdown from './Dropdown.vue';
import ConfirmAddToShoppingList from '../Modals/ConfirmAddToShoppingList.vue';
import ConfirmMarkAsDoneModal from '../Modals/ConfirmMarkAsDoneModal.vue';

const props = defineProps({
    recipeId: {type: Number, required: true},
    translations: {type: Object, required: true},
});

const dropdown = ref(null);
const isConfirmAddToShoppingListOpen = ref(false);
const isConfirmMarkAsDoneOpen = ref(false);

function openShoppingListModal() {
    isConfirmAddToShoppingListOpen.value = true;
    dropdown.value.close();
}

function openMarkAsDoneModal() {
    isConfirmMarkAsDoneOpen.value = true;
    dropdown.value.close();
}
</script>

<template>
    <Dropdown ref="dropdown">
        <template #trigger>
            <button class="button">
                <i class="pi pi-ellipsis-v"></i>
            </button>
        </template>

        <li>
            <button @click="openShoppingListModal">
                <i class="pi pi-list-check"/>
                {{ translations.shopping_list.add_to_list }}
            </button>
        </li>
        <li>
            <button @click="openMarkAsDoneModal">
                <i class="pi pi-check"/>
                Atzīmēt kā pabeigtu
            </button>
        </li>
    </Dropdown>

    <ConfirmAddToShoppingList
        v-model="isConfirmAddToShoppingListOpen"
        :title="translations.shopping_list.add_to_list"
        :message="translations.shopping_list.ask_confirm"
        :recipeId="recipeId"
    />

    <ConfirmMarkAsDoneModal
        v-model="isConfirmMarkAsDoneOpen"
        title="Vai vēlies atzīmēt recpeti kā izdarītu?"
        message="No tavas mājsaimniecības tiks atņemtas visas receptē esošās sastāvdaļas."
        :recipeId="recipeId"
    />
</template>
