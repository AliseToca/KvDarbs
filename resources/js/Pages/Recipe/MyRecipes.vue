<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import Pagination from "../../Components/Pagination.vue";
import SearchBar from "../../Components/Inputs/SearchBar.vue";
import NotFound from "../../Components/NotFound.vue";

const { translations, recipes, recipe_count, filters} = usePage().props;
</script>

<template>
    <MainLayout>
        <div class="my-recipes">
            <header>
                <div>
                    <h1>{{ translations.recipe.my_recipes.title }}</h1>
                    <span class="header-info">{{ recipe_count }} {{ translations.recipe.plural }}</span>
                </div>

                <div class="header-actions">
                    <SearchBar :model-value="filters.search" placedHolder="Meklē receptes..." />

                    <Link :href="route('recipe.create')" as="button" class="button primary">
                        <i class="pi pi-plus"/>
                    </Link>
                </div>
            </header>


            <div class="grid-container">
                <RecipeCard
                    v-for="recipe in recipes.data || []"
                    :key="recipe.id"
                    :id="recipe.id"
                    :name="recipe.name"
                    :url="route('recipes.edit', { recipe: recipe.slug })"
                    :imageSrc="recipe.image_src"
                    :rating="recipe.average_rating"
                    :reviews_count=recipe.reviews_count
                    :time_minutes="recipe.total_time"
                    :servings=recipe.servings
                    :show_missing_products="false"
                />
            </div>

            <NotFound
                v-if="recipes.data.length === 0"
                iconClass="pi pi-search-minus"
                :title="translations.recipe.not_found"
                :message = "translations.recipe.not_found_message"
            />

            <Pagination :links="recipes.links" />
        </div>
    </MainLayout>
</template>
