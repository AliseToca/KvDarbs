<script setup>
import {computed, ref} from 'vue';
import Dropdown from '../Dropdowns/Dropdown.vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: null,
    },
    options: {
        type: Array,
        required: true,
        // [{ value: String, label: String, icon: String }]
    },
});

const emit = defineEmits(['update:modelValue']);

const active = computed(
    () => props.options.find(o => o.value === props.modelValue) ?? props.options[0]
);

function select(value) {
    emit('update:modelValue', value);
}

const dropdown = ref(null);
const isOpen = ref(false);
</script>

<template>
    <Dropdown class="sort-by" ref="dropdown" :class="{ 'is-open': isOpen }" @close="isOpen = false">        <template #trigger>
            <button type="button" class="sort-trigger" @click="isOpen = !isOpen">
                <span><i :class="active.icon"></i> {{ active.label }}</span>
                <i class="pi pi-chevron-down chevron"></i>
            </button>
        </template>

        <li
            v-for="opt in options"
            :key="opt.value"
            class="dropdown-item"
            :class="{ 'dropdown-item--active': modelValue === opt.value }"
            @click="select(opt.value); dropdown.close()"
        >
            <span class="dropdown-item-label">
                <i :class="opt.icon"></i>
                <span>{{ opt.label }}</span>
            </span>
            <i v-if="modelValue === opt.value" class="pi pi-check check-icon"/>
        </li>
    </Dropdown>
</template>
