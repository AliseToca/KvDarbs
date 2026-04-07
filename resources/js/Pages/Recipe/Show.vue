<script setup>
import {usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import MainLayout from '../../Layouts/Main.vue';
import FormatTime from '../../Components/FormatTime.vue';
import Review from "../../Components/Review.vue";
import ReviewForm from "../../Components/ReviewForm.vue";
import Pagination from "../../Components/Pagination.vue";
import Breadcrumb from "../../Components/Breadcrumb.vue";
import RecipeActionsDropdown from "../../Components/Dropdowns/RecipeActionsDropdown.vue";
import ConfirmDeleteModal from "../../Components/Modals/ConfirmDeleteModal.vue";
import Rating from "../../Components/Rating.vue";
import Avatar from "../../Components/Avatar.vue";

const {translations, recipe, breadcrumbs} = usePage().props;

//--Atsauksmes--
const reviews = computed(() => usePage().props.reviews.data || []);

const openConfirmDeleteModal = ref(false);
const selectedReviewId = ref(null);

function openDelete(id) {
    selectedReviewId.value = id;
    openConfirmDeleteModal.value = true;
}

//--Porciju skaits--
const currentServings = ref(recipe.servings);

function decrementServings() {
    currentServings.value > 1 ? currentServings.value-- : null;
}

function incrementServings() {
    currentServings.value++;
}

//Sastāvdaļas atbilstoši pašreizējam porciju skaitam
const scaledIngredients = computed(() => {
    return recipe.recipe_products.map(ingredient => {
        const baseServings = recipe.servings;

        //Daudzuma aprēķins
        const scaledAmount = ((ingredient.amount / baseServings) * currentServings.value).toFixed(2);

        return {
            ...ingredient, //sastāvdaļas lauki
            scaledAmount,
        };
    });
});

// Sastāvdaļu daudzuma noapaļošana ar 2 cipariem aiz komata, ja ir daļskaitlis
function formatAmount(value) {
    const number = Number(value);

    if (Number.isNaN(number)) return '';

    const rounded = Math.round(number * 100) / 100;

    return Number.isInteger(rounded)
        ? rounded
        : rounded.toFixed(2);
}
</script>

<template>
    <MainLayout>
        <div class="recipe">
            <header class="page-header">
                <Breadcrumb :items="breadcrumbs"/>
                <RecipeActionsDropdown :recipeId="recipe.id" :translations="translations"/>
            </header>

            <section class="recipe-header">
                <div class="recipe-header-content">
                    <header>
                        <h1>{{ recipe.name }}</h1>

                        <div class="recipe-header-info">
                            <Rating :rating="recipe.average_rating"/>

                            <span class="rating-value">
                                {{ recipe.average_rating ? recipe.average_rating.toFixed(1) : '0.0' }}
                                ({{ recipe.reviews_count }}
                                {{ translations.recipe.reviews.plural }})
                            </span>

                            <span class="divider">•</span>

                            <span class="author">
                                <Avatar :avatarSrc="recipe.user.avatar_src" mini/>
                                {{ recipe.user.username }}
                            </span>
                        </div>
                    </header>

                    <div class="time-grid">
                        <div class="time-item">
                            <span class="label">{{ translations.recipe.prep_time }}</span>
                            <span class="value">
                                <FormatTime :timeMinutes="recipe.prep_time"/>
                            </span>
                        </div>

                        <div class="time-item">
                            <span class="label">{{ translations.recipe.cook_time }}</span>
                            <span class="value">
                                <FormatTime :timeMinutes="recipe.cook_time"/>
                            </span>
                        </div>

                        <div class="time-item">
                            <span class="label">{{ translations.recipe.total_time }}</span>
                            <span class="value">
                                <FormatTime :timeMinutes="recipe.total_time"/>
                            </span>
                        </div>
                    </div>

                    <div class="recipe-tags">
                        <div class="tag-group">
                            <span class="label">{{ translations.recipe.types }}</span>
                            <div class="tags">
                                <span class="tag">{{ recipe.recipe_type?.name }}</span>
                            </div>
                        </div>

                        <div class="tag-group">
                            <span class="label">{{ translations.recipe.categories }}</span>
                            <div class="tags">
                                <span  v-for="category in recipe.recipe_categories"  class="tag">
                                    {{ category.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <img :src="recipe.image_src ? `/storage/${recipe.image_src}` : '/storage/placeholder.jpg'">
            </section>

            <section class="recipe-ingredients">
                <div class="ingredients-header">
                    <h3>{{ translations.recipe.ingredients }}</h3>

                    <!--Porciju skaita samazināšana/palielināšana-->
                    <div class="servings">
                        <button @click="decrementServings">-</button>
                        <span>{{ currentServings }}</span>
                        <button @click="incrementServings">+</button>
                    </div>
                </div>
                <!--Sastāvdaļus saraksts atbilstoši poricju skaitam-->
                <div v-for="ingredient in scaledIngredients" :key="ingredient.id" class="ingredient">
                    <input type="checkbox" class="ingredient-checkbox">
                    <span>
                        {{ formatAmount(ingredient.scaledAmount) }}{{ ingredient.unit.name }}
                        {{ ingredient.product.name }}
                      </span>
                </div>
            </section>

            <section class="recipe-instructions">
                <h3>{{ translations.recipe.instructions }}</h3>
                <ol>
                    <!--Izvada pagatavošanas soļus ar numuru-->
                    <li v-for="(step, index) in recipe.instructions" :key="index">
                        {{ index + 1 }}. {{ step }}
                    </li>
                </ol>
            </section>

            <section class="recipe reviews">
                <h2>{{ translations.recipe.reviews.heading }}</h2>
                <ReviewForm/>
            </section>

            <section>
                <h3>{{ translations.recipe.reviews.plural }}</h3>
                <Review
                    v-for="review in reviews"
                    :key="review.id"
                    :id="review.id"
                    :rating="review.rating"
                    :content="review.content"
                    :username="review.user.username"
                    :avatarSrc="review.user.avatar_src"
                    :createdAt="review.created_at"
                    @delete="openDelete(review.id)"
                />
                <ConfirmDeleteModal
                    v-model="openConfirmDeleteModal"
                    :title="translations.recipe.reviews.plural"
                    message="Vai tiešām vēlies dzēst atsauksmi?"
                    route-name="recipes.reviews.delete"
                    :route-param="selectedReviewId"
                />
                <Pagination :links="usePage().props.reviews.links"/>
            </section>
        </div>
    </MainLayout>
</template>
