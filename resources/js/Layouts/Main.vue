<script setup>
    import Header from "./Partials/Header.vue"
</script>

<template>
    <div>
        <Header/>
        <main>
            <slot></slot>
        </main>
    </div>
</template>

