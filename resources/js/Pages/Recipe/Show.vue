<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import FormatTime from '../../Components/FormatTime.vue';
import Checkbox from "../../Components/Inputs/Checkbox.vue";

const trans = usePage().props.translations;
const recipe = usePage().props.recipe;
</script>

<template>
    <MainLayout>
        <div class="recipe">
            <section class="recipe header">
                <div class="recipe left">
                    <h1>{{ recipe.name }}</h1>
                    <div>Rating & Comments</div>

                    <p><strong>{{ trans.recipe.prep_time }}: </strong><FormatTime :timeMinutes="recipe.prep_time"/></p>
                    <p><strong>{{ trans.recipe.cook_time }}: </strong><FormatTime :timeMinutes="recipe.cook_time"/></p>
                    <p><strong>{{ trans.recipe.total_time }}: </strong><FormatTime :timeMinutes="recipe.total_time"/></p>

                    <p><strong>{{ trans.recipe.author }}: </strong><span>autors</span></p>

                    <div>
                        <button>Print</button>
                        <button>Save</button>
                    </div>
                </div>
                <img :src="'/storage/'+ recipe.image_src">
            </section>
            <section class="recipe ingredients">
                <div class="ingredients-header">
                    <h3>{{ trans.recipe.ingredients }}</h3>
                    <p>serving count</p>
                </div>
                <div v-for="ingredient in recipe.recipe_products" :key="ingredient.id">
                    <Checkbox/>
                    {{ ingredient.amount }}{{ ingredient.unit.name }} {{ ingredient.product.name }}
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
