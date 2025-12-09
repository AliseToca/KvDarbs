<script setup>
    const props = defineProps({
        id: String,
        name: String,
        label: String,
        modelValue: Boolean,
    });

    const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <input
        type="checkbox"
        :id="id"
        :name="name"
        :checked="modelValue"
        @change="emit('update:modelValue', $event.target.checked)"
    />
    <label :for="id" class="checkbox-wrapper">{{label}}</label>
</template>
