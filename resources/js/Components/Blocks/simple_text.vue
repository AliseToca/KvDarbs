<script setup>
defineProps({
    text: {
        type: String,
        required: false,
        default: ''
    }
})
</script>

<template>
    <div class="simple-text-block">
        {{ text }}
    </div>
</template>
