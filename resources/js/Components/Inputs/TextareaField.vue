<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: String,
    maxLength: Number,
});

const emit = defineEmits(['update:modelValue']);

const text = computed({
    get: () => props.modelValue,
    set: val => emit('update:modelValue', val),
});

const charCount = computed(() => text.value.length);
</script>

<template>
  <textarea
      v-model="text"
      :placeholder="placeholder"
      :maxlength="maxLength"
  ></textarea>

    <div v-if="maxLength !== undefined" class="char-count">
        {{ charCount }} / {{ maxLength }}
    </div>
</template>
