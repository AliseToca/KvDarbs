<script setup>
const props = defineProps({
    type: String,
    id: String,
    name: String,
    label: String,
    placeholderValue: String,
    modelValue: String,
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <label :for="id">{{ label }}</label>
    <input
        :type="type"
        :id="id"
        :name="name"
        :placeholder="placeholderValue"
        :value="modelValue"
        @input="emit('update:modelValue', $event.target.value)"
    />
</template>
