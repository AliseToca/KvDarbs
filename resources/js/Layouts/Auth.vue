<script setup>
    import AuthHeader from "./Partials/AuthHeader.vue"
</script>

<template>
    <div class="main-layout">
        <AuthHeader/>
        <main>
            <slot></slot>
        </main>
    </div>
</template>

