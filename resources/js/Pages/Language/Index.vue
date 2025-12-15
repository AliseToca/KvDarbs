<script>
    import AppLayout from '@/Layouts/Main.vue';

    export default {
        layout: AppLayout,
    };

</script>

<script setup>
    const {page,content,blocks} = defineProps({
        page: Object,
        content: [String, Object],
        blocks: Array
    })

</script>

<template>
    <h1>{{ page.name }}</h1>
    <p>{{ content }}</p>
</template>
