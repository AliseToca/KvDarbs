<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage} from "@inertiajs/vue3";
import RecipeCard from "../../Components/RecipeCard.vue";
import Breadcrumb from "../../Components/Breadcrumb.vue";
import EditFolderModal from "../../Components/Modals/EditFolderModal.vue";
import {computed, ref} from "vue";

const { translations } = usePage().props;

const folder = computed(() => usePage().props.folder);
const breadcrumbs = computed(() => usePage().props.breadcrumbs);

const showEditModal = ref(false);
</script>

<template>
    <MainLayout>
        <Breadcrumb :items="breadcrumbs" />
        <header class="folder-header">
            <div class="folder-info-container">
                <i class="pi pi-folder-open"/>
                <div class="folder-info">
                    <h1>{{ folder.name }}</h1>
                    <p class="folder-count">
                        {{ folder.recipes.length }} {{ folder.recipes.length === 1 ? translations.recipe.singular : translations.recipe.plural }}
                    </p>
                </div>
            </div>

            <div class="folder-actions">
                <button class="button">
                    <i class="pi pi-print"/>
                </button>
                <button class="button primary" @click="showEditModal = true">
                    <i class="pi pi-file-edit"/>
                    {{ translations.button.edit }}
                </button>
            </div>

            <EditFolderModal
                v-model="showEditModal"
            />
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

        <div v-else class="empty-folder">
            {{ translations.folders.empty_folder }}
        </div>
    </MainLayout>
</template>
