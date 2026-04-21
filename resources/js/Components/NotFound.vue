<script setup>
defineProps({
    iconClass: String,
    title: String,
    message: String,
})
</script>

<template>
    <div class="not-found">
        <div class="not-found-icon">
            <i :class="iconClass"/>
        </div>

        <div class="not-found-text">
            <h4>{{title}}</h4>
            <span>{{ message }}</span>
        </div>
    </div>
</template>
