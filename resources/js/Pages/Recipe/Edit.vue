<script setup>
import { ref } from 'vue';
import { usePage, useForm, Link } from "@inertiajs/vue3";
import MainLayout from '../../Layouts/Main.vue';
import Breadcrumb from "../../Components/Breadcrumb.vue";
import RecipeForm from "../../Components/RecipeForm.vue";
import DeleteRecipeModal from "../../Components/Modals/DeleteRecipeModal.vue";

const { translations, products, units, breadcrumbs, recipe } = usePage().props;

const showDeleteModal = ref(false);

const form = useForm({
    name: recipe.name,
    image_src: recipe.image_src,
    prep_time: recipe.prep_time,
    cook_time: recipe.cook_time,
    servings: recipe.servings,
    recipe_products: recipe.recipe_products.map(rp => ({
        product_id: rp.product.id,
        amount: rp.amount,
        unit_id: rp.unit.id,
    })),
    instructions: recipe.instructions,
    visibility: recipe.visibility,
});

function submit() {
    form.post(route('recipes.update', recipe.slug), {
        forceFormData: true,
    });
}
</script>

<template>
    <MainLayout>
        <div class="recipe">
            <Breadcrumb :items="breadcrumbs" />
            <header>
                <h1>{{ translations.recipe.edit_recipe }}</h1>

                <button class="button primary" @click="showDeleteModal = true">
                    <i class="pi pi-trash"/> {{ translations.button.delete }}
                </button>
            </header>
            <RecipeForm :form="form" :products="products" :units="units" @submit="submit" />
        </div>

        <DeleteRecipeModal
            v-model="showDeleteModal"
            :recipe="recipe"
        />
    </MainLayout>
</template>
