<script setup>
import {usePage, router} from '@inertiajs/vue3';
import {ref} from 'vue';
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import Pagination from "../../Components/Pagination.vue";
import SearchBar from "../../Components/Inputs/SearchBar.vue";
import ConstructorRenderer from "../../Components/ConstructorRenderer.vue";
import NotFound from "../../Components/NotFound.vue";
import SortBy from "../../Components/Inputs/SortBy.vue";
import CategorySelect from "../../Components/Inputs/CategorySelect.vue";
import AvailableToggle from "../../Components/Inputs/AvailableToggle.vue";

const {translations, recipes, blocks, filters, page_name, types, categories} = usePage().props;

console.log(blocks);
const urlParams = new URLSearchParams(window.location.search);
const resultsKey = ref(0);

const selectedType = ref(Number(urlParams.get('type')) || null);
const selectedSort = ref(urlParams.get('sort') || 'newest');
const selectedCategories = ref(
    urlParams.get('categories')
        ? urlParams.get('categories').split(',').map(Number).filter(Boolean)
        : []
);

const availableOnly = ref(urlParams.get('available') === 'true');

function selectType(id) {
    selectedType.value = id;
    applyFilters();
}

function clearAll() {
    selectedType.value = null;
    selectedCategories.value = [];
    availableOnly.value = false;
    applyFilters();
}

function removeCategory(id) {
    selectedCategories.value = selectedCategories.value.filter(v => v !== id);
    applyFilters();
}

function applyFilters() {
    router.get(
        window.location.pathname,
        {
            search: filters.search || undefined,
            type: selectedType.value || undefined,
            sort: selectedSort.value !== 'newest' ? selectedSort.value : undefined,
            categories: selectedCategories.value.length
                ? selectedCategories.value.join(',')
                : undefined,
            available: availableOnly.value || undefined,
        },
        {
            preserveScroll: true,
            replace: true,
            onSuccess: () => { resultsKey.value++; },
        }
    );
}
</script>

<template>
    <MainLayout>
        <ConstructorRenderer :pageName="page_name" :blocks="blocks"/>

        <div class="recipe-page">
            <SearchBar
                :model-value="filters.search"
                :placedHolder="translations.recipe.find_recipes"
            />

            <!-- Filter bar: type pills left, controls right -->
            <div class="filters-bar">
                <div class="filters-left tag-selector">
                    <div class="tags-wrap">
                        <span
                            class="tag tag-selectable"
                            :class="{ selected: selectedType === null }"
                            @click="clearAll"
                        >
                            {{ translations.recipe.all_recipes }}
                        </span>
                        <span
                            v-for="type in types"
                            :key="type.id"
                            class="tag tag-selectable"
                            :class="{ selected: selectedType === type.id }"
                            @click="selectType(type.id)"
                        >
                            {{ type.name }}
                        </span>
                    </div>
                </div>

                <div class="filters-right">
                    <AvailableToggle
                        v-model="availableOnly"
                        :label="translations.recipe.available_only"
                        @update:modelValue="applyFilters"
                    />
                    <CategorySelect
                        v-if="categories && categories.length"
                        v-model="selectedCategories"
                        :categories="categories"
                        :placeholder="translations.recipe.categories"
                        @close="applyFilters"
                    />
                    <SortBy
                        v-model="selectedSort"
                        :options="[
                            { value: 'highest_rated', label: translations.recipe.sort_by.highest_rating, icon: 'pi pi-star-fill' },
                            { value: 'most_reviewed', label: translations.recipe.sort_by.most_ratings, icon: 'pi pi-comments' },
                            { value: 'quickest', label: translations.recipe.sort_by.quickest, icon: 'pi pi-clock' },
                            { value: 'newest', label: translations.recipe.sort_by.newest, icon: 'pi pi-chart-line' },
                        ]"
                        @update:modelValue="applyFilters"
                    />
                </div>
            </div>

            <!-- Active category tags row -->
            <div v-if="selectedCategories.length" class="active-tags">
                <span
                    v-for="cat in categories.filter(c => selectedCategories.includes(c.id))"
                    :key="cat.id"
                    class="tag tag-active"
                >
                    {{ cat.name }}
                    <i class="pi pi-times tag-remove" @click="removeCategory(cat.id)"></i>
                </span>
            </div>

            <div :key="resultsKey" class="results-fade">
                <TransitionGroup name="card-fade" tag="div" class="grid-container">
                    <RecipeCard
                        v-for="recipe in recipes.data || []"
                        :key="recipe.id"
                        :id="recipe.id"
                        :url="recipe.url"
                        :name="recipe.name"
                        :imageSrc="recipe.image_src"
                        :rating="recipe.average_rating"
                        :reviews_count="recipe.reviews_count"
                        :time_minutes="recipe.total_time"
                        :missing_products_count="recipe.missing_products_count"
                        :available_products_count="recipe.available_products_count"
                        :total_products_count="recipe.total_products_count"
                        :compatibility="recipe.compatibility"
                        :servings="recipe.servings"
                    />
                </TransitionGroup>

                <Transition name="fade" mode="out-in">
                    <NotFound
                        v-if="recipes.data.length === 0"
                        iconClass="pi pi-search-minus"
                        :title="translations.recipe.not_found"
                        :message="translations.recipe.not_found_message"
                    />
                </Transition>
            </div>

            <Pagination :links="recipes.links"/>
        </div>
    </MainLayout>
</template>
