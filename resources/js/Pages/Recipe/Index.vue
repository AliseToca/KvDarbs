<script setup>
import {usePage, router} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import MainLayout from '../../Layouts/Main.vue';
import RecipeCard from '../../Components/RecipeCard.vue';
import Pagination from "../../Components/Pagination.vue";
import SearchBar from "../../Components/SearchBar.vue";
import ConstructorRenderer from "../../Components/ConstructorRenderer.vue";
import NotFound from "../../Components/NotFound.vue";
import SortBy from "../../Components/SortBy.vue";

const {translations, recipes, blocks, filters, page_name, types} = usePage().props;


const urlParams = new URLSearchParams(window.location.search);
const resultsKey = ref(0);

const selectedType = ref(Number(urlParams.get('type')) || null);
const selectedSort = ref(urlParams.get('sort') || 'newest');

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
            sort: selectedSort.value !== 'newest' ? selectedSort.value : undefined,
        },
        {
            preserveScroll: true,
            replace: true,
            onSuccess: () => {
                resultsKey.value++;
            },
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

            <div class="filters-bar">
                <div class="tag-selector">
                    <div v-if="types && types.length" class="tag-group">
                        <div class="tags-wrap">
                        <span
                            class="tag tag-selectable"
                            @click="clearAll"
                            :class="{ selected: selectedType === null }"
                        >
                            {{ translations.recipe.all_recipes }}
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

                <div>
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


            <div :key="resultsKey" class="results-fade">
                <TransitionGroup
                    name="card-fade"
                    tag="div"
                    class="grid-container"
                >
                    <RecipeCard
                        v-for="recipe in recipes.data || []"
                        :key="recipe.id"
                        :id="recipe.id"
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
