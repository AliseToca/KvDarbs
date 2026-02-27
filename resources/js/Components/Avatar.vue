<script setup>
defineProps({
    avatarSrc: {
        type: String,
        default: null,
    },
});

defineEmits(['click']);
</script>

<template>
    <div class="avatar" @click="$emit('click')">
        <img v-if="avatarSrc" :src="`/storage/${avatarSrc}`"/>

        <button v-else class="button round primary">
            <i class="pi pi-user"></i>
        </button>
    </div>
</template>
