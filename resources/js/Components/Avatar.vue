<script setup>
const props = defineProps({
    avatarSrc: {
        type: String,
        default: null,
    },
    small: {
        type: Boolean,
        default: false,
    },
    mini: {
        type: Boolean,
        default: false,
    },
    clickable: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['click']);
</script>

<template>
    <div class="avatar" :class="{ 'small': small, 'mini': mini, 'clickable': clickable }">
        <img v-if="avatarSrc" :src="`/storage/${avatarSrc}`"/>

        <div v-else class="placeholder">
            <i class="pi pi-user"></i>
        </div>
    </div>
</template>
