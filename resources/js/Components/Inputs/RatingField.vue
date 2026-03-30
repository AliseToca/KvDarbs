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
    <div class="rating-stars">
        <span
            v-for="star in 5"
            :key="star"
            class="rating-star"
            :class="{ active: star <= hover || star <= modelValue }"
            @click="update(star)"
            @mouseenter="hover = star"
            @mouseleave="hover = 0"
        >
            <i class="pi pi-star-fill"></i>
        </span>
    </div>
</template>
