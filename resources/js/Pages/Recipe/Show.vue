<script setup>
import MainLayout from '../../Layouts/Main.vue';

import { usePage } from '@inertiajs/vue3';

const recipe = usePage().props.recipe;

</script>

<template>
    <MainLayout>
        <h1>{{ recipe.name }}</h1>
        <p>hello recipe</p>
    </MainLayout>
</template>
