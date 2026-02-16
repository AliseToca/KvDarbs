<script setup>
import { usePage } from '@inertiajs/vue3';
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import Pagination from "../../Components/Pagination.vue";
import SearchBar from "../../Components/SearchBar.vue";
import ConstructorRenderer from "../../Components/ConstructorRenderer.vue";

const { translations, recipes, filters, blocks, page_name} = usePage().props;
</script>

<template>
    <MainLayout>
        <ConstructorRenderer :pageName="page_name" :blocks="blocks"/>

        <div>
            <SearchBar
                :model-value="filters.search"
                placedHolder="Meklē receptes..."
            />
        </div>

        <div class="grid-container">
            <RecipeCard
                v-for="recipe in recipes.data || []"
                :key="recipe.id"
                :url = recipe.url
                :name = recipe.name
                :imageSrc = recipe.image_src
                :rating = recipe.average_rating
                :time_minutes = recipe.total_time
                :missing_products_count = recipe.missing_products_count
                :compatibility = recipe.compatibility
            />

            <p v-if="recipes.data.length === 0">
                {{ translations.recipe.not_found }}
            </p>

        </div>

        <Pagination :links="recipes.links" />
    </MainLayout>
</template>
