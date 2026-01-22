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
    maxLength: {Number},
    lengthCheck: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const errorList = computed(() => {
    if (!props.error) return [];
    return Array.isArray(props.error) ? props.error : [props.error];
});

const charCount = computed(() => {
    return props.modelValue?.length || 0;
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
            :maxlength="maxLength"
        />

        <div v-if="maxLength !== undefined" class="char-count">
            {{ charCount }} / {{ maxLength }}
        </div>

        <ul class="error-container">
            <li v-for="(err, i) in errorList" :key="i" class="error-message">
                {{ err }}
            </li>
        </ul>
    </div>
</template>
