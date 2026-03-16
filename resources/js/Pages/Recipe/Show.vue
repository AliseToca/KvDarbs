<script setup>
import {router, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import MainLayout from '../../Layouts/Main.vue';
import FormatTime from '../../Components/FormatTime.vue';
import Review from "../../Components/Review.vue";
import ReviewForm from "../../Components/ReviewForm.vue";
import Pagination from "../../Components/Pagination.vue";
import Breadcrumb from "../../Components/Breadcrumb.vue";
import Dropdown from "../../Components/Dropdown.vue";
import ConfirmAddToShoppingList from "../../Components/Modals/ConfirmAddToShoppingList.vue";

const { translations, recipe, breadcrumbs} = usePage().props;

const dropdown = ref(null);
const isConfirmAddToShoppingListOpen = ref(false);

function openShoppingListModal() {
    isConfirmAddToShoppingListOpen.value = true;
    dropdown.value.close();
}

const reviews = computed(() => usePage().props.reviews.data || []);

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
        <Breadcrumb :items="breadcrumbs"/>

        <div class="recipe">
            <section class="recipe header">
                <div class="recipe header-content">
                    <div>
                        <header>
                            <h1>{{ recipe.name }}</h1>

                            <Dropdown ref="dropdown">
                                <template #trigger>
                                    <button class="button">
                                        <i class="pi pi-ellipsis-v"></i>
                                    </button>
                                </template>

                                <li>
                                    <button @click="openShoppingListModal">
                                        <i class="pi pi-list-check"/>
                                        {{ translations.shopping_list.add_to_list }}
                                    </button>
                                </li>
                            </Dropdown>

                            <ConfirmAddToShoppingList
                                v-model="isConfirmAddToShoppingListOpen"
                                :title="translations.shopping_list.add_to_list"
                                :message="translations.shopping_list.ask_confirm"
                                :recipeId="recipe.id"
                            />
                        </header>
                        <span>
                            <i class="pi pi-star"></i>
                            {{ recipe.average_rating || 0 }}

                            <i class="pi pi-comments"></i>
                            {{ recipe.reviews_count }}
                        </span>
                    </div>
                    <img :src="recipe.image_src ? `/storage/${recipe.image_src}` : '/storage/placeholder.jpg'">

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
                            <span>@{{ recipe.user.username }}</span>
                        </p>
                    </div>
                </div>
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
                        {{index + 1}}. {{ step }}
                    </li>
                </ol>
            </section>

            <section class="recipe reviews">
                <h2>{{ translations.recipe.reviews.heading }}</h2>
                <ReviewForm/>
                <Review
                    v-for="review in reviews"
                    :key="review.id"
                    :id="review.id"
                    :rating = "review.rating"
                    :content = "review.content"
                    :username = "review.user.username"
                    :created_at = "review.created_at"
                />
                <Pagination :links="usePage().props.reviews.links" />

            </section>
        </div>
    </MainLayout>
</template>
