<script setup>
import MainLayout from '../../Layouts/Main.vue';

const { page, content, blocks } = defineProps({
        page: Object,
        content: [String, Object],
        blocks: String,
    });
</script>


<template>
    <MainLayout>
        <h1>{{ page.name }}</h1>
        <div class="blocks" v-html="blocks"></div>
    </MainLayout>
</template>
