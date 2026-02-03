<script setup>
import { ref } from 'vue'

const props = defineProps({
    modelValue: { type: Number, default: 0 },
})

const emit = defineEmits(['update:modelValue'])
const hover = ref(0)

function update(value) {
    emit('update:modelValue', value)
}
</script>

<template>
    <div class="stars">
        <span
            v-for="star in 5"
            :key="star"
            class="star"
            :class="{ active: star <= hover || star <= modelValue }"
            @click="update(star)"
            @mouseenter="hover = star"
            @mouseleave="hover = 0"
        >
          <i class="pi pi-star-fill"></i>
        </span>
    </div>
</template>

<style scoped>
.stars {
    font-size: 32px;
    cursor: pointer;
}
</style>
