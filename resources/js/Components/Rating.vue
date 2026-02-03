<script setup>
const props = defineProps({
    rating: Number,
})
</script>

<template>
    <div class="stars">
        <span
            v-for="star in 5"
            :key="star"
            class="star"
            :class="{ active: star <= rating }"
        >
          <i class="pi pi-star-fill"></i>
        </span>
    </div>
</template>

<style scoped>
.stars {
    font-size: 16px;
}
</style>
