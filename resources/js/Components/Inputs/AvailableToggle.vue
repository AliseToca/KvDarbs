<script setup>
const props = defineProps({
    modelValue: Boolean,
    label: String,
});
const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <button
        type="button"
        class="available-toggle"
        :class="{ 'is-active': modelValue }"
        @click="emit('update:modelValue', !modelValue)"
    >
        <span class="toggle-label">{{ label }}</span>
        <span class="toggle-track">
            <span class="toggle-thumb"></span>
        </span>
    </button>
</template>
