<script setup>
import {computed} from 'vue'
import {usePage} from "@inertiajs/vue3";

const {translations} = usePage().props;

const props = defineProps({
    folder: {
        type: Object,
        required: true,
    },
})

defineEmits(['click'])

const images = computed(() => {
    const withImage = props.folder.recipes
        .filter(r => r.image_src)
        .map(r => r.image_src)
    return Array.from({length: 3}, (_, i) => withImage[i] ?? null)
})

const recipeCount = computed(() => {
    const count = props.folder.recipes.length

    return `${count} ${count === 1 ? translations.recipe.singular : translations.recipe.plural}`
})
</script>

<template>
    <div class="folder-card" @click="$emit('click', folder)">
        <div class="images-grid">
            <div class="img-main-wrap">
                <img v-if="images[0]" :src="images[0]" class="img-main" :alt="folder.name"/>
                <div v-else class="placeholder"/>
            </div>
            <div class="img-side">
                <div class="img-quarter-wrap">
                    <img v-if="images[1]" :src="images[1]" class="img-quarter" :alt="folder.name"/>
                    <div v-else class="placeholder"/>
                </div>
                <div class="img-quarter-wrap">
                    <img v-if="images[2]" :src="images[2]" class="img-quarter" :alt="folder.name"/>
                    <div v-else class="placeholder"/>
                </div>
            </div>
        </div>

        <div class="folder-meta">
            <p class="folder-name">{{ folder.name }}</p>
            <p class="folder-count">{{ recipeCount }}</p>
        </div>
    </div>
</template>
