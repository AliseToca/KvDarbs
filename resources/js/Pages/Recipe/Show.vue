<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import FormatTime from '../../Components/FormatTime.vue';
import Review from "../../Components/Review.vue";
import ReviewForm from "../../Components/ReviewForm.vue";
import Pagination from "../../Components/Pagination.vue";

const { translations, recipe, reviews} = usePage().props;

const currentServings = ref(recipe.servings);

function decrementServings() {
    currentServings.value > 1 ? currentServings.value-- : null;
}

function incrementServings() {
    currentServings.value++;
}

//Sastāvdaļas atbilstoši pašreizējam porciju skaitam
const scaledIngredients = computed(() => {
    return recipe.recipe_products.map(ingredient =>{
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
            <section class="recipe header">
                <div class="recipe header-content">
                    <div>
                        <h1>{{ recipe.name }}</h1>
                        <span>
                            <i class="pi pi-star"></i> {{recipe.average_rating}}
                            <i class="pi pi-comments"></i> {{recipe.reviews_count}}
                        </span>
                    </div>
                    <div>
                        <!--Receptes gatavošanas ilgumi stundās un minūtēs-->
                        <p>
                            <strong>{{ translations.recipe.prep_time }}:</strong>
                            <FormatTime :timeMinutes="recipe.prep_time"/>
                        </p>
                        <p>
                            <strong>{{ translations.recipe.cook_time }}: </strong>
                            <FormatTime :timeMinutes="recipe.cook_time"/>
                        </p>
                        <p>
                            <strong>{{ translations.recipe.total_time }}: </strong>
                            <FormatTime :timeMinutes="recipe.total_time"/>
                        </p>
                        <!--Receptes autors-->
                        <p>
                            <strong>{{ translations.recipe.author }}: </strong>
                            <span>autors</span>
                        </p>
                    </div>
                </div>
                <img :src="'/storage/'+ recipe.image_src">
            </section>

            <section class="recipe header-buttons">
                <button class="button primary full-width">Print</button>
                <button class="button full-width">Save</button>
            </section>

            <section class="recipe ingredients">
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
                <div v-for="ingredient in scaledIngredients" :key="ingredient.id">
                    {{ formatAmount(ingredient.scaledAmount) }}{{ ingredient.unit.name }} {{ ingredient.product.name }}
                </div>
            </section>

            <section class="recipe instructions">
                <h3>{{ translations.recipe.instructions }}</h3>
                <ol>
                    <!--Izvada pagatavošanas soļus ar numuru-->
                    <li v-for="(step, index) in recipe.instructions" :key="index">
                        {{index + 1}}. {{ step.text }}
                    </li>
                </ol>
            </section>

            <section class="recipe reviews">
                <h2>{{ translations.recipe.reviews.heading }}</h2>
                <ReviewForm/>
                <Review
                    v-for="review in reviews.data"
                    :key="review.id"
                    :id="review.id"
                    :rating = "review.rating"
                    :content = "review.content"
                    :username = "review.user.username"
                    :created_at = "review.created_at"
                />
                <Pagination :links="reviews.links" />

            </section>
        </div>
    </MainLayout>
</template>
