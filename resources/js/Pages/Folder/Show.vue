<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage} from "@inertiajs/vue3";
import RecipeCard from "../../Components/RecipeCard.vue";
import Breadcrumb from "../../Components/Breadcrumb.vue";

const { translations, folder, breadcrumbs } = usePage().props;
</script>

<template>
    <MainLayout>
        <Breadcrumb :items="breadcrumbs" />
        <header class="folder-header">
            <div>
                <h1>{{ folder.name }}</h1>
                <p class="folder-count">
                    {{ folder.recipes.length }} {{ folder.recipes.length === 1 ? translations.recipe.singular : translations.recipe.plural }}
                </p>
            </div>

            <div class="folder-actions">
                <button class="button">
                    <i class="pi pi-print"/>
                </button>
                <button class="button primary">
                    <i class="pi pi-file-edit"/>
                    {{ translations.button.edit }}
                </button>
            </div>

        </header>

        <div v-if="folder.recipes.length" class="grid-container">
            <RecipeCard
                v-for="recipe in folder.recipes || []"
                :key="recipe.id"
                :url = recipe.url
                :name = recipe.name
                :imageSrc = recipe.image_src
                :rating = recipe.average_rating
                :time_minutes = recipe.total_time
            />
        </div>
    </MainLayout>
</template>
