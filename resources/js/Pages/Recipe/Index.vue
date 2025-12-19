<script setup>
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import { usePage } from '@inertiajs/vue3';

const recipes = usePage().props.recipes;
</script>

<template>
    <MainLayout>
        <RecipeCard
            name = "Test"
            :rating = 4.5
            :time_minutes = 70
            :compatibility = 70
        />
    </MainLayout>
</template>
