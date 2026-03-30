<script setup>
const props = defineProps({
    rating: Number,
});
</script>

<template>
    <div class="stars">
        <span v-for="star in 5" :key="star" class="star">
            <i class="pi pi-star-fill empty"></i>

            <i
                v-if="rating >= star"
                class="pi pi-star-fill full"
            ></i>

            <i
                v-else-if="rating >= star - 0.5"
                class="pi pi-star-half-fill half"
            ></i>
        </span>
    </div>
</template>

<style scoped>
.stars {
    font-size: 16px;
    display: flex;
}
</style>
