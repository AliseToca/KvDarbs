<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import RecipeCard from "../../Components/RecipeCard.vue";
import Breadcrumb from "../../Components/Breadcrumb.vue";
import EditFolderModal from "../../Components/Modals/EditFolderModal.vue";
import Dropdown from "../../Components/Dropdowns/Dropdown.vue";
import ConfirmDeleteModal from "../../Components/Modals/ConfirmDeleteModal.vue";

const { translations } = usePage().props;

const folder = computed(() => usePage().props.folder);
const breadcrumbs = computed(() => usePage().props.breadcrumbs);

const showEditModal = ref(false);
const showConfirmDelete = ref(false);
const dropdown = ref(null);
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

            <Dropdown ref="dropdown">
                <template #trigger>
                    <button class="button">
                        <i class="pi pi-ellipsis-v"/>
                    </button>
                </template>

                <li>
                    <button @click="dropdown?.close(); showEditModal = true">
                        <i class="pi pi-file-edit"/>
                        {{ translations.button.edit }}
                    </button>
                </li>
                <li>
                    <button @click="dropdown?.close(); showConfirmDelete = true">
                        <i class="pi pi-trash"/>
                        {{ translations.button.delete }}
                    </button>
                </li>
            </Dropdown>

            <EditFolderModal
                v-model="showEditModal"
            />

            <ConfirmDeleteModal
                v-model="showConfirmDelete"
                :title="translations.folders.folder"
                :message="translations.folders.delete_message"
                route-name="folders.destroy"
                :route-param="folder"
            />
        </header>

        <div v-if="folder.recipes.length" class="grid-container">
            <RecipeCard
                v-for="recipe in folder.recipes || []"
                :key="recipe.id"
                :url="`${recipe.url}?from_folder=${folder.id}`"
                :name="recipe.name"
                :imageSrc="recipe.image_src"
                :rating="recipe.average_rating"
                :time_minutes="recipe.total_time"
                :missing_products_count="recipe.missing_products_count"
                :available_products_count="recipe.available_products_count"
                :total_products_count="recipe.total_products_count"
                :compatibility="recipe.compatibility"
            />
        </div>

        <div v-else class="empty-folder">
            {{ translations.folders.empty_folder }}
        </div>
    </MainLayout>
</template>
