<script>
    import MainLayout from '@/Layouts/Main.vue';
    export default {
        layout: MainLayout,
    };
</script>

<script setup>
    const { page, content, blocks } = defineProps({
        page: Object,
        content: [String, Object],
        blocks: String,
    });
</script>

<template>
        <h1>{{page.name}}</h1>
<!--         <div class="blocks" v-html="blocks"></div>-->
</template>


