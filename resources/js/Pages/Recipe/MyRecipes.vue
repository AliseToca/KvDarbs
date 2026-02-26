<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import Pagination from "../../Components/Pagination.vue";
import SearchBar from "../../Components/SearchBar.vue";

const { translations, recipes, filters} = usePage().props;
</script>

<template>
    <MainLayout>
        <div class="my-recipes">
            <header>
                <h1>{{ translations.recipe.my_recipes.title }}</h1>

                <Link :href="route('recipe.create')" as="button" class="button primary">
                    <i class="pi pi-plus"/>
                </Link>
            </header>

            <SearchBar :model-value="filters.search" placedHolder="Meklē receptes..." />

            <div class="grid-container">
                <RecipeCard
                    v-for="recipe in recipes.data || []"
                    :key="recipe.id"
                    :name="recipe.name"
                    :url="route('recipes.edit', { recipe: recipe.slug })"
                    :imageSrc="recipe.image_src"
                    :rating="recipe.average_rating"
                    :time_minutes="recipe.total_time"
                    :missing_products_count="recipe.missing_products_count"
                    :compatibility="recipe.compatibility"
                />

                <p v-if="recipes.data.length === 0">
                    {{ translations.recipe.not_found }}
                </p>
            </div>

            <Pagination :links="recipes.links" />
        </div>
    </MainLayout>
</template>
