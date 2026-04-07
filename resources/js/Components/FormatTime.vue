<script setup>
import { computed } from 'vue';

const props = defineProps({
    timeMinutes: Number,
})

// Laika formatēšana uz stundām un minutēm
const hours = computed(() => Math.floor(props.timeMinutes / 60));
const mins = computed(() => props.timeMinutes % 60);
</script>

<template>
    <span v-if="hours > 0">{{hours}}h </span>
    <span v-if="mins >= 0">{{mins}}min</span>
</template>
