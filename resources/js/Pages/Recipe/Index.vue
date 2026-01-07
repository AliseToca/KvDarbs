<script setup>
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import { usePage } from '@inertiajs/vue3';

const recipes = usePage().props.recipes;
console.log(recipes);
</script>

<template>
    <MainLayout>
        <div class="grid-container">
            <RecipeCard
                v-for="recipe in recipes" :key="recipe.id"
                :name = recipe.name
                :rating = recipe.rating
                :time_minutes = recipe.time_minutes
                :compatibility = 70
            />
            <RecipeCard
                v-for="test in 10"
                name = "Test"
                :rating = 4.5
                :time_minutes = 70
                :compatibility = 70
            />
        </div>
    </MainLayout>
</template>
