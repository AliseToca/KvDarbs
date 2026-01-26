<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    items: {
        type: Array,
        default: () => [],
    },
    label: String,
    placeholderValue: String,
    notFoundMessage: String,
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
    { immediate: true }
);

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
</script>


<template>
        <div class="select">
            <label>{{ label }}</label>
            <input
                type="text"
                v-model="search"
                :placeholder="placeholderValue"
                @focus="isOpen = true"
                @input="emit('update:modelValue', search.value)"
            />

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
