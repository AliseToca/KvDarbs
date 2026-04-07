<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import MainLayout from '../../Layouts/Main.vue';
import Breadcrumb from "../../Components/Breadcrumb.vue";
import RecipeForm from "../../Components/RecipeForm.vue";

const { translations, products, units, breadcrumbs } = usePage().props;

const form = useForm({
    name: '',
    image_src: null,
    prep_time: 0,
    cook_time: 0,
    servings: 1,
    recipe_products: [
        {
            product_id: '',
            amount: '',
            unit_id: '',
        }
    ],
    instructions: [''],
    visibility: 'household',
    recipe_type_id: null,
    recipe_category_ids: [],
});

function submit() {
    form.post(route('recipes.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <MainLayout>
        <div class="recipe">
            <Breadcrumb :items="breadcrumbs" />
            <h1>{{ translations.recipe.create_recipe }}</h1>
            <RecipeForm :form="form" :products="products" :units="units" @submit="submit" />
        </div>
    </MainLayout>
</template>
