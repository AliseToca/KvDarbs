<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import FormatTime from '../../Components/FormatTime.vue';

const recipe = usePage().props.recipe;
</script>

<template>
    <MainLayout>
        <div>
            <div>
                <h1>{{ recipe.name }}</h1>
                <div>
                    <p>Prep time: <FormatTime :timeMinutes="recipe.prep_time"/></p>
                    <p>Cook time: <FormatTime :timeMinutes="recipe.cook_time"/></p>
                    <p>Total time: <FormatTime :timeMinutes="recipe.total_time"/></p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
