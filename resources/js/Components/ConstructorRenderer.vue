<script setup>
const props = defineProps({
    pageName: String,
    blocks: {
        type: Array,
        required: true,
        default: () => []
    }
});

const blockComponents = import.meta.glob('./Blocks/*.vue', {
    eager: true,
});

const resolveBlock = (type) => {
    const componentPath = `./Blocks/${type}.vue`
    return blockComponents[componentPath]?.default ?? null
};
</script>

<template>
    <h1>{{ pageName }}</h1>
    <div v-if="blocks?.length">
        <component
            v-for="(block, index) in blocks"
            :key="block.id ?? index"
            :is="resolveBlock(block.type)"
            v-bind="block.data"
        />
    </div>
</template>
