<script setup>
import {ref, computed} from "vue";

const props = defineProps({
    label: String,
    placeholderValue: String,
    items: Array,
    modelValue: [String, Number],
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);

const selectedItem = computed(() => {
    return props.items.find(
        item => Number(item.id) === Number(props.modelValue)
    ) || null
});

function selectItems(item) {
    emit('update:modelValue', item.id);
    isOpen.value = false;
}
</script>

<template>
    <div class="select" >
        <label> {{ label }}</label>

        <div class="select-wrapper">
            <input
                type="text"
                :placeholder="placeholderValue"
                :value="selectedItem?.name || ''"
                @focus="isOpen = true"
                readonly
            />

            <i class="pi pi-chevron-down" :class="{'open': isOpen}"></i>
        </div>

        <ul v-if="isOpen" class="dropdown">
            <li
                v-for="item in items"
                :key="item.id"
                @click="selectItems(item)"
            >
                {{ item.name }}
            </li>

            <li v-if="items.length === 0" class="empty">
                {{ notFoundMessage }}
            </li>
        </ul>
    </div>
</template>
