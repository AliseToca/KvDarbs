<script setup>
import {usePage} from "@inertiajs/vue3";

const { translations, types, categories } = usePage().props;

const props = defineProps({
    selectedTypeId: { type: Number, default: null },
    selectedCategoryIds: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:selectedTypeId', 'update:selectedCategoryIds']);

function toggleType(id) {
    emit('update:selectedTypeId', props.selectedTypeId == id ? null : id);
}

function toggleCategory(id) {
    const updated = props.selectedCategoryIds.includes(id)
        ? props.selectedCategoryIds.filter(c => c !== id)
        : [...props.selectedCategoryIds, id];
    emit('update:selectedCategoryIds', updated);
}
</script>

<template>
    <div class="tag-selector">
        <div class="tag-group">
            <label class="section-label">{{ translations.recipe.types }}</label>
            <div class="tags-wrap">
                <span
                    v-for="type in types"
                    :key="type.id"
                    class="tag tag-selectable"
                    :class="{ selected: selectedTypeId === type.id }"
                    @click="toggleType(type.id)"
                >
                    {{ type.name }}
                </span>
            </div>
        </div>

        <div class="tag-group">
            <label class="section-label">{{ translations.recipe.categories }}</label>
            <div class="tags-wrap">
                <span
                    v-for="category in categories"
                    :key="category.id"
                    class="tag tag-selectable"
                    :class="{ selected: selectedCategoryIds.includes(category.id) }"
                    @click="toggleCategory(category.id)"
                >
                    {{ category.name }}
                </span>
            </div>
        </div>
    </div>
</template>
