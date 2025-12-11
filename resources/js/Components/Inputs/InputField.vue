<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: String,
    id: String,
    name: String,
    label: String,
    placeholderValue: String,
    modelValue: String,
    error: [String, Array],
});

const emit = defineEmits(['update:modelValue']);

// Normalize error into an array (always)
const errorList = computed(() => {
    if (!props.error) return [];
    return Array.isArray(props.error) ? props.error : [props.error];
});
</script>

<template>
    <div>
        <label :for="id">{{ label }}</label>

        <input
            :type="type"
            :id="id"
            :name="name"
            :placeholder="placeholderValue"
            :value="modelValue"
            @input="emit('update:modelValue', $event.target.value)"
            :class="{ 'input-error': errorList.length }"
        />

        <ul class="error-container">
            <li v-for="(err, i) in errorList" :key="i" class="error-message">
                {{ err }}
            </li>
        </ul>
    </div>
</template>
