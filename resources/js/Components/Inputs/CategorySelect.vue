<script setup>
import {ref, computed, onMounted, onBeforeUnmount} from "vue";

const props = defineProps({
    categories: Array,
    modelValue: Array,
    placeholder: String,
});

const emit = defineEmits(['update:modelValue', 'close']);

const isOpen = ref(false);
const search = ref('');
const container = ref(null);

const filteredCategories = computed(() =>
    props.categories.filter(c =>
        c.name.toLowerCase().includes(search.value.toLowerCase())
    )
);

function toggle(category) {
    const isSelected = props.modelValue.includes(category.id);
    emit('update:modelValue', isSelected
        ? props.modelValue.filter(v => v !== category.id)
        : [...props.modelValue, category.id]
    );
}

function openDropdown() {
    isOpen.value = true;
    search.value = '';
}

function handleClickOutside(e) {
    if (container.value && !container.value.contains(e.target)) {
        if (isOpen.value) {
            isOpen.value = false;
            emit('close');
        }
    }
}

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('mousedown', handleClickOutside));
</script>

<template>
    <div class="category-select" :class="{ 'is-open': isOpen }" ref="container">

        <!-- Trigger — matches SortBy style -->
        <div class="sort-trigger" @click="openDropdown">
            <span class="trigger-label">
                <i class="pi pi-sliders-h"></i>
                {{ placeholder }}
            </span>
            <span v-if="modelValue.length" class="count-badge">{{ modelValue.length }}</span>
            <i class="pi pi-chevron-down chevron"></i>
        </div>

        <!-- Dropdown -->
        <div v-if="isOpen" class="cat-dropdown">
            <div class="cat-search-wrap">
                <i class="pi pi-search cat-search-icon"></i>
                <input
                    v-model="search"
                    class="cat-search"
                    type="text"
                    autofocus
                    @click.stop
                />
            </div>

            <ul class="cat-options">
                <li
                    v-for="cat in filteredCategories"
                    :key="cat.id"
                    class="cat-option"
                    :class="{ 'is-selected': modelValue.includes(cat.id) }"
                    @click="toggle(cat)"
                >
                    <span>{{ cat.name }}</span>
                    <i v-if="modelValue.includes(cat.id)" class="pi pi-check cat-check"></i>
                </li>
                <li v-if="filteredCategories.length === 0" class="cat-option cat-empty">
                    No results
                </li>
            </ul>
        </div>
    </div>
</template>
