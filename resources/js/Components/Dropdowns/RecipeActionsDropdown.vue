<script setup>
import {ref} from 'vue';
import Dropdown from './Dropdown.vue';
import ConfirmAddToShoppingList from '../Modals/ConfirmAddToShoppingList.vue';
import ConfirmMarkAsDoneModal from '../Modals/ConfirmMarkAsDoneModal.vue';
import AddToFolderModal from "../Modals/AddToFolderModal.vue";
import {Link, usePage} from "@inertiajs/vue3";

const {translations, user} = usePage().props;

const props = defineProps({
    recipe: {
        type: Object,
        required: true
    },
    translations: {
        type: Object,
        required: true
    },
});

const dropdown = ref(null);
const isConfirmAddToShoppingListOpen = ref(false);
const isConfirmMarkAsDoneOpen = ref(false);
const isConfirmAddToFolderOpen = ref(false);
</script>

<template>
    <Dropdown ref="dropdown">
        <template #trigger>
            <button class="button">
                <i class="pi pi-ellipsis-v"></i>
            </button>
        </template>

        <li>
            <button @click="isConfirmAddToShoppingListOpen = true; dropdown.close()">
                <i class="pi pi-list-check"/>
                {{ translations.recipe.actions.add_to_shopping_list }}
            </button>
        </li>
        <li>
            <button @click="isConfirmMarkAsDoneOpen = true; dropdown.close()">
                <i class="pi pi-check"/>
                {{ translations.recipe.actions.mark_as_done }}
            </button>
        </li>
        <li>
            <a :href="route('recipes.pdf', recipe)" target="_blank">
                <i class="pi pi-file-pdf"/>
                {{ translations.recipe.export_recipe }}
            </a>
        </li>
        <li>
            <button @click="isConfirmAddToFolderOpen = true; dropdown.close()">
                <i class="pi pi-bookmark"/>
                {{ translations.recipe.actions.save }}
            </button>
        </li>
        <li v-if="user.id === recipe.user_id">
            <Link :href="route('recipes.edit', { recipe: recipe.slug })">
                <i class="pi pi-pen-to-square"/>
                {{ translations.recipe.edit_recipe }}
            </Link>
        </li>
    </Dropdown>

    <ConfirmAddToShoppingList
        v-model="isConfirmAddToShoppingListOpen"
        :title="translations.shopping_list.add_to_list"
        :message="translations.shopping_list.ask_confirm"
        :recipeId="recipe.id"
    />

    <ConfirmMarkAsDoneModal
        v-model="isConfirmMarkAsDoneOpen"
        title="Vai vēlies atzīmēt recepti kā izdarītu?"
        message="No tavas mājsaimniecības tiks atņemtas visas receptē esošās sastāvdaļas."
        :recipeId="recipe.id"
    />

    <AddToFolderModal
        v-model="isConfirmAddToFolderOpen"
    />
</template>
