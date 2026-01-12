<script setup>
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import { usePage } from '@inertiajs/vue3';

const recipes = usePage().props.recipes;
</script>

<template>
    <MainLayout>
        <div class="grid-container">
            <RecipeCard
                v-for="recipe in recipes" :key="recipe.id"
                :url = recipe.url
                :name = recipe.name
                :rating = recipe.rating
                :time_minutes = recipe.total_time
                :compatibility = 70
            />
        </div>
    </MainLayout>
</template>
