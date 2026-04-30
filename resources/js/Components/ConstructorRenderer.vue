<script setup>
const props = defineProps({
    pageName: String,
    blocks: Array,
});
</script>

<template>
    <h1>{{ pageName }}</h1>
    <div class="blocks">
        <template v-for="(block, index) in blocks" :key="index">
            <!-- simple_text -->
            <div v-if="block.type === 'simple_text'" v-html="block.data.text" />

            <!-- add more block types here as needed -->
            <!-- <div v-else-if="block.type === 'image'" ... /> -->
        </template>
    </div>
</template>
