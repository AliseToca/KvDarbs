<script setup>
import { computed } from 'vue';
import {Link} from "@inertiajs/vue3";
import FormatTime from "./FormatTime.vue";

const props = defineProps({
    url: String,
    slug: String,
    name: String,
    imageSrc: String,
    rating: Number,
    time_minutes: Number,
    compatibility: Number,
})
</script>

<template>
    <Link
        :href="url"
    >
        <div class="recipe-card">
            <img src="../../assets/images/placeholder.jpg">
            <div class="recipe-card-item header">
                <h3>{{ name }}</h3>
                <div>
                    <i class="pi pi-star-fill"></i>
                    <span>{{ rating }}</span>
                </div>
            </div>
            <div class="recipe-card-item tags">
                <div class="tag">
                    <FormatTime :timeMinutes="time_minutes"/>
                </div>
                <div class="tag">Nav 2 sastāvdaļu</div>
            </div>
            <div class="compatibility-bar">
                <div
                    class="compatibility-bar fill"
                    :style="{ width: compatibility + '%' }"
                ></div>
            </div>
        </div>
    </Link>
</template>
