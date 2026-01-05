<script setup>
import { computed } from 'vue';

const props = defineProps({
    imageSrc: String,
    name: String,
    rating: Number,
    time_minutes: Number,
    compatibility: Number,
})

// Laika aprēķins no stundām uz minutēm
const hours = computed(() => Math.floor(props.time_minutes / 60));
const mins = computed(() => props.time_minutes % 60);
</script>

<template>
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
                <span v-if="hours > 0">{{hours}}h </span>
                <span v-if="mins > 0">{{mins}}min</span>
            </div>
            <div class="tag">Nav 2 sastāvdāļu</div>
        </div>
        <div class="compatibility-bar">
            <div
                class="compatibility-bar fill"
                :style="{ width: compatibility + '%' }"
            ></div>
        </div>
    </div>
</template>
