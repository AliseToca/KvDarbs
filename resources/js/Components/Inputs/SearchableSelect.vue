<script setup>
import {ref, computed, watch, onMounted, onBeforeUnmount} from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    items: {
        type: Array,
        default: () => [],
    },
    label: String,
    placeholderValue: String,
    notFoundMessage: String,
    disabled: {
        type: Boolean,
        default: false,
    },
    disabledPlaceholder: String,
    error: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

const search = ref(props.modelValue);
const isOpen = ref(false);

watch(
    () => props.modelValue,
    (id) => {
        if (!id) {
            search.value = '';
            return;
        }
        const item = props.items.find(i => i.id === id);
        search.value = item ? item.name : '';
    },
    {immediate: true}
);

watch(search, (val) => {
    if (!val) emit('update:modelValue', '');
});

const filteredItems = computed(() => {
    if (!search.value) return props.items;

    return props.items.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

function selectItems(item) {
    search.value = item.name;
    emit('update:modelValue', item.id);
    isOpen.value = false;
}

const container = ref(null);

function handleClickOutside(e) {
    if (container.value && !container.value.contains(e.target)) {
        isOpen.value = false;
    }
}

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('mousedown', handleClickOutside));
</script>

<template>
    <div class="select" ref="container">
        <label>{{ label }}</label>
        <div class="input-wrapper">
            <input
                type="text"
                v-model="search"
                :placeholder="disabled ? disabledPlaceholder : placeholderValue"
                @focus="isOpen = true"
                :disabled="disabled"
            />

            <span class="select-arrow" :class="{ 'select-arrow--open': isOpen }">
                <i
                    class="pi select-arrow"
                    :class="isOpen ? 'pi-chevron-up' : 'pi-chevron-down'"
                />
            </span>
        </div>

        <span v-if="error" class="error-message">{{ error }}</span>

        <ul v-if="isOpen" class="dropdown">
            <li
                v-for="item in filteredItems"
                :key="item.id"
                @click="selectItems(item)"
            >
                {{ item.name }}
            </li>

            <li v-if="filteredItems.length === 0" class="empty">
                {{ notFoundMessage }}
            </li>
        </ul>
    </div>
</template>
