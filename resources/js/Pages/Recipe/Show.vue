<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import FormatTime from '../../Components/FormatTime.vue';
import Checkbox from "../../Components/Inputs/Checkbox.vue";
import {computed, ref} from 'vue';

const trans = usePage().props.translations;
const recipe = usePage().props.recipe;

const currentServings = ref(recipe.servings);

function decrementServings() {
    if (currentServings.value > 1) {
        currentServings.value--;
    }
}

function incrementServings() {
    currentServings.value++;
}

const scaledIngredients = computed(() => {
    return recipe.recipe_products.map(ingredient =>{
        const baseServings = recipe.servings;

        const scaledAmount = ((ingredient.amount / baseServings) * currentServings.value).toFixed(2);

        return {
            ...ingredient,
            scaledAmount,
        };
    });
});

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
                        <p>Rating & Comments</p>
                    </div>
                    <div>
                        <p><strong>{{ trans.recipe.prep_time }}: </strong><FormatTime :timeMinutes="recipe.prep_time"/></p>
                        <p><strong>{{ trans.recipe.cook_time }}: </strong><FormatTime :timeMinutes="recipe.cook_time"/></p>
                        <p><strong>{{ trans.recipe.total_time }}: </strong><FormatTime :timeMinutes="recipe.total_time"/></p>
                        <p><strong>{{ trans.recipe.author }}: </strong><span>autors</span></p>
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
                    <h3>{{ trans.recipe.ingredients }}</h3>
                    <div class="servings">
                        <button @click="decrementServings">-</button>
                        <span>{{currentServings}}</span>
                        <button @click="incrementServings">+</button>
                    </div>
                </div>
                <div v-for="ingredient in scaledIngredients" :key="ingredient.id">
                    {{ formatAmount(ingredient.scaledAmount) }}{{ ingredient.unit.name }} {{ ingredient.product.name }}
                </div>
            </section>
            <section>
                <h3>{{ trans.recipe.instructions }}</h3>
                <ol>
                    <li v-for="(step, index) in recipe.instructions" :key="index">
                        {{index + 1}}. {{ step.text }}
                    </li>
                </ol>
            </section>
        </div>
    </MainLayout>
</template>
