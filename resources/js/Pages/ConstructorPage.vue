<script>
    import MainLayout from '@/Layouts/Main.vue';

    export default {
        layout: MainLayout,
    };

</script>

<script setup>
    const props = defineProps(['page'])

</script>

<template>
        <h1>{{page.name}}</h1>

</template>


