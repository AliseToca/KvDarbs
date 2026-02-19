<script setup>
import { ref } from 'vue';
import {route} from "ziggy-js";
import {router, usePage, useForm} from '@inertiajs/vue3';
import TextareaField from './Inputs/TextareaField.vue';
import RatingField from "./Inputs/RatingField.vue";

const { translations, recipe } = usePage().props;

const form = useForm({
    rating: 0,
    content: '',
});

function submitReview() {
    form.post(route('recipes.reviews.store', recipe.slug), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            router.reload({ only: ['reviews'], preserveScroll: true });
        },
    });
}
</script>

<template>
    <form class="form-field" @submit.prevent="submitReview">
        <div class="form-rating-container">
            <RatingField
                v-model="form.rating"
            />
            <span v-if="form.errors.rating" class="error-text">{{ form.errors.rating }}</span>
        </div>

        <TextareaField
            v-model="form.content"
            :placeholder="translations.recipe.reviews.add_review"
            :max-length="1000"
        />

        <span v-if="form.errors.content" class="error-text">{{ form.errors.content }}</span>

        <button class="button" type="submit" :disabled="form.processing">
            {{ translations.button.submit }}
        </button>
    </form>
</template>

