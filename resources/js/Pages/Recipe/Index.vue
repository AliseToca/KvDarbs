<script setup>
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Pagination from "../../Components/Pagination.vue";

const { recipes } = usePage().props;
</script>

<template>
    <MainLayout>
        <div class="grid-container">
            <RecipeCard
                v-for="recipe in recipes.data || []"
                :key="recipe.id"
                :url = recipe.url
                :name = recipe.name
                :rating = recipe.average_rating
                :time_minutes = recipe.total_time
                :compatibility = 70
            />
        </div>

        <Pagination :links="recipes.links" />
    </MainLayout>
</template>
