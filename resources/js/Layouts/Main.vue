<script setup>
    import Header from "./Partials/Header.vue"
    import Footer from "./Partials/Footer.vue"
</script>

<template>
    <div class="main-layout">
        <Header/>
        <main class="container">
            <slot></slot>
        </main>
        <Footer/>
    </div>
</template>

