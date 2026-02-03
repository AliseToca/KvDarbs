<script setup>
import { ref } from 'vue';
import {route} from "ziggy-js";
import {router, usePage} from '@inertiajs/vue3';
import TextareaField from './Inputs/TextareaField.vue';
import RatingField from "./Inputs/RatingField.vue";

const { translations } = usePage().props;

const reviewText = ref('');
const rating = ref(0);

const submitReview = () => {
    router.post(route('recipes.reviews.store', usePage().props.recipe.slug), {
        rating: rating.value,
        content: reviewText.value,
    }, {
        onSuccess: () => {
            reviewText.value = ''
            rating.value = 0

            router.visit(window.location.href, {
                preserveScroll: true,
                preserveState: false,
            });
        },
    })
}

</script>

<template>
    <form class="form-field" @submit.prevent="submitReview">
        <div class="form-rating-container">
            <RatingField
                v-model="rating"
            />
        </div>

        <TextareaField
            v-model="reviewText"
            :placeholder="translations.recipe.reviews.add_review"
            :max-length="1000"
        />
        <button class="button" type="submit">
            {{ translations.button.submit }}
        </button>
    </form>
</template>

