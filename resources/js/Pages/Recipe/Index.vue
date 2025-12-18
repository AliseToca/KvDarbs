<script setup>
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import { usePage } from '@inertiajs/vue3';

const recipes = usePage().props.recipes;
</script>

<template>
    <MainLayout>

    </MainLayout>
</template>
