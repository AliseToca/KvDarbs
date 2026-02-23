<script setup>
import Header from "./Partials/Header.vue";
import Footer from "./Partials/Footer.vue";
import FlashMessage from "../Components/Modals/FlashMessage.vue";
</script>

<template>
    <div class="main-layout">
        <Header/>
        <main class="container">
            <FlashMessage/>
            <slot></slot>
        </main>
        <Footer/>
    </div>
</template>

