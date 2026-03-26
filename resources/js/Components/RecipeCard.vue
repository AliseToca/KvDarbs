<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import FormatTime from "./FormatTime.vue";

const { translations } = usePage().props;

const props = defineProps({
    url: String,
    slug: String,
    name: String,
    imageSrc: String,
    rating: Number,
    time_minutes: Number,
    missing_products_count: Number,
    compatibility: Number,
    show_missing_products: {
        type: Boolean,
        default: true,
    },
})
</script>

<template>
    <Link
        :href="url"
    >
        <div class="recipe-card">
            <img :src="imageSrc ? `/storage/${imageSrc}` : '/storage/placeholder.jpg'">
            <div class="recipe-card-item header">
                <h3>{{ name }}</h3>
                <div>
                    <span v-if="rating">
                        <i class="pi pi-star-fill"></i>
                        {{ rating }}
                    </span>

                    <span v-else>
                        {{ translations.recipe.reviews.no_rating }}
                    </span>
                </div>
            </div>
            <div class="recipe-card-item tags">
                <div class="tag">
                    <FormatTime :timeMinutes="time_minutes"/>
                </div>
                <div v-show="show_missing_products" class="tag">
                    {{ missing_products_count > 0
                    ? `Nav ${missing_products_count} sastāvdaļu`
                    : 'Visas sastāvdaļas pieejamas '
                    }}
                </div>
            </div>
            <div v-show="show_missing_products" class="compatibility-bar">
                <div
                    class="compatibility-fill"
                    :style="{ width: compatibility + '%' }"
                ></div>
            </div>
        </div>
    </Link>
</template>
