<script setup>
import {usePage, router} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import Pagination from "../../Components/Pagination.vue";
import SearchBar from "../../Components/SearchBar.vue";
import ConstructorRenderer from "../../Components/ConstructorRenderer.vue";
import NotFound from "../../Components/NotFound.vue";

const {translations, recipes, filters, blocks, page_name, categories, types} = usePage().props;

const urlParams = new URLSearchParams(window.location.search);

const selectedType = ref(Number(urlParams.get('type')) || null);

function selectType(id) {
    selectedType.value = id;
    applyFilters();
}

function clearAll() {
    selectedType.value = null;
    applyFilters();
}

function applyFilters() {
    router.get(
        window.location.pathname,
        {
            search: filters.search || undefined,
            type: selectedType.value || undefined,
        },
        { preserveScroll: true, replace: true }
    );
}
</script>

<template>
    <MainLayout>
        <ConstructorRenderer :pageName="page_name" :blocks="blocks"/>

        <div class="recipe-page">
            <SearchBar
                :model-value="filters.search"
                placedHolder="Meklē receptes..."
            />

            <div class="tag-selector">
                <div v-if="types && types.length" class="tag-group">
                    <div class="tags-wrap">
                        <span
                            class="tag tag-selectable"
                            @click="clearAll"
                            :class="{ selected: selectedType === null }"
                        >
                            Visas receptes
                        </span>
                        <span
                            v-for="type in types"
                            :key="type.id"
                            class="tag tag-selectable"
                            :class="{ selected: selectedType === type.id }"
                            @click="selectType(type.id)"
                        >{{ type.name }}</span>
                    </div>
                </div>
            </div>

            <div class="grid-container">
                <RecipeCard
                    v-for="recipe in recipes.data || []"
                    :key="recipe.id"
                    :url=recipe.url
                    :name=recipe.name
                    :imageSrc=recipe.image_src
                    :rating=recipe.average_rating
                    :reviews_count=recipe.reviews_count
                    :time_minutes=recipe.total_time
                    :missing_products_count=recipe.missing_products_count
                    :available_products_count=recipe.available_products_count
                    :total_products_count=recipe.total_products_count
                    :compatibility=recipe.compatibility
                    :servings=recipe.servings
                />
            </div>

            <NotFound
                v-if="recipes.data.length === 0"
                iconClass="pi pi-search-minus"
                :title="translations.recipe.not_found"
                :message = "translations.recipe.not_found_message"
            />

            <Pagination :links="recipes.links"/>
        </div>
    </MainLayout>
</template>
